<?php

namespace App\Services\Notifications;

use App\Models\DeliveryRider;
use App\Models\OnlineOrder;
use App\Models\RiderDispatchRequest;
use App\Models\TrackingSession;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\Fcm\Exceptions\FcmException;
use App\Services\Fcm\Exceptions\FcmInvalidTokenException;
use App\Services\Fcm\FcmClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * High-level push notification service on top of FcmClient.
 *
 * Builds event-specific payloads (trip/order/dispatch/payment/message),
 * resolves recipients, sends to every active device of each recipient and
 * cleans up tokens FCM reports as unregistered.
 */
class NotificationService
{
    public const STAFF_ROLES = ['admin', 'manager', 'marketing_officer', 'storekeeper'];

    public function __construct(private readonly FcmClient $fcm) {}

    public function isEnabled(): bool
    {
        return $this->fcm->isConfigured();
    }

    /**
     * Send to a single device. Handles expired-token cleanup.
     *
     * @param  array<string, string>  $extra  data payload keys (deep-link info)
     * @param  array<string, mixed>  $platformOptions
     * @return array<string, mixed> ['status' => 'sent'|'deactivated'|'failed']
     */
    public function sendToDevice(
        UserDevice $device,
        string $type,
        string $screen,
        string $title,
        string $body,
        array $extra = [],
        array $platformOptions = []
    ): array {
        if (! $this->isEnabled() || ! $device->fcm_token) {
            return ['status' => 'skipped', 'device_id' => $device->id];
        }

        $data = [
            'type' => $type,
            'screen' => $screen,
            'title' => $title,
            'body' => $body,
            'sent_at' => now()->toIso8601String(),
            ...$extra,
        ];

        try {
            $this->fcm->send($device->fcm_token, [
                'title' => $title,
                'body' => $body,
            ], $data, $platformOptions);

            $device->forceFill(['last_used_at' => now()])->save();

            return ['status' => 'sent', 'device_id' => $device->id];
        } catch (FcmInvalidTokenException $e) {
            Log::warning('FCM: deactivating device with expired/invalid token', [
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'error' => $e->getMessage(),
            ]);

            $device->update([
                'is_active' => false,
                'fcm_token' => null,
                'last_used_at' => now(),
            ]);

            return ['status' => 'deactivated', 'device_id' => $device->id];
        } catch (FcmException $e) {
            Log::error('FCM: notification failed', [
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return ['status' => 'failed', 'device_id' => $device->id];
        }
    }

    /**
     * Send to every active device (with an FCM token) of a single user.
     *
     * @return array<string, mixed>
     */
    public function sendToUser(
        User|int $user,
        string $type,
        string $screen,
        string $title,
        string $body,
        array $extra = [],
        array $platformOptions = []
    ): array {
        $userId = $user instanceof User ? $user->id : $user;

        $devices = UserDevice::query()
            ->where('user_id', $userId)
            ->active()
            ->hasFcmToken()
            ->get();

        return $this->dispatchToDevices($devices, $type, $screen, $title, $body, $extra, $platformOptions);
    }

    /**
     * Send to many users, skipping those without a registered device.
     *
     * @param  iterable<User|int>  $users
     * @return array<string, mixed>
     */
    public function sendToUsers(
        iterable $users,
        string $type,
        string $screen,
        string $title,
        string $body,
        array $extra = [],
        array $platformOptions = []
    ): array {
        $result = ['sent' => 0, 'deactivated' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($users as $user) {
            $outcome = $this->sendToUser($user, $type, $screen, $title, $body, $extra, $platformOptions);

            $result['sent'] += $outcome['sent'];
            $result['deactivated'] += $outcome['deactivated'];
            $result['failed'] += $outcome['failed'];
            $result['skipped'] += $outcome['skipped'];
        }

        return $result;
    }

    public function sendOrderNotification(OnlineOrder $order, string $event): array
    {
        $copy = $this->orderCopy($order, $event);

        if ($copy === null) {
            return ['sent' => 0, 'deactivated' => 0, 'failed' => 0, 'skipped' => 0];
        }

        $recipients = $this->staffUsers();

        // Notify the customer as well when they have a registered account.
        if ($order->user_id) {
            $recipients->push($order->user);
        }

        return $this->sendToUsers($recipients, $copy['type'], $copy['screen'], $copy['title'], $copy['body'], $copy['extra']);
    }

    public function sendPaymentNotification(OnlineOrder $order, string $event): array
    {
        $copy = $this->paymentCopy($order, $event);

        if ($copy === null) {
            return ['sent' => 0, 'deactivated' => 0, 'failed' => 0, 'skipped' => 0];
        }

        $recipients = $this->staffUsers();

        if ($order->user_id) {
            $recipients->push($order->user);
        }

        return $this->sendToUsers($recipients, $copy['type'], $copy['screen'], $copy['title'], $copy['body'], $copy['extra']);
    }

    public function sendTripNotification(TrackingSession $session, string $event): array
    {
        $copy = $this->tripCopy($session, $event);

        if ($copy === null) {
            return ['sent' => 0, 'deactivated' => 0, 'failed' => 0, 'skipped' => 0];
        }

        $recipients = $this->staffUsers();

        if ($session->order?->user_id) {
            $recipients->push($session->order->user);
        }

        return $this->sendToUsers($recipients, $copy['type'], $copy['screen'], $copy['title'], $copy['body'], $copy['extra']);
    }

    /**
     * Notify online riders about a new broadcast dispatch request.
     */
    public function sendDispatchRequestNotification(RiderDispatchRequest $dispatch): array
    {
        $order = $dispatch->order;
        $title = 'New Delivery Request';
        $body = $order
            ? 'New order '.$order->order_number.' is available — accept it to start delivering.'
            : 'A new delivery request is available.';

        $riders = DeliveryRider::query()
            ->where('is_online', true)
            ->whereHas('user', function ($query) {
                $query->whereHas('devices', fn ($q) => $q->active()->hasFcmToken());
            })
            ->with('user')
            ->get();

        return $this->sendToUsers($riders->pluck('user'), 'dispatch.request.new', 'dispatch', $title, $body, [
            'order_id' => (string) ($order->id ?? ''),
            'order_number' => (string) ($order->order_number ?? ''),
        ]);
    }

    public function sendMessageNotification(User $recipient, string $senderName, string $preview): array
    {
        return $this->sendToUser($recipient, 'message.new', 'chat', 'New Message', $senderName.': '.$preview);
    }

    /**
     * Broadcast an announcement to users matching the given roles.
     *
     * @param  string[]  $roles
     */
    public function sendAnnouncement(string $title, string $body, array $roles = [], string $type = 'announcement', string $screen = 'announcements'): array
    {
        $users = User::query()
            ->whereHas('devices', fn ($q) => $q->active()->hasFcmToken());

        if ($roles !== []) {
            $users->whereIn('role', $roles);
        }

        return $this->sendToUsers($users->get(), $type, $screen, $title, $body);
    }

    /**
     * @return Collection<int, User>
     */
    private function staffUsers()
    {
        return User::query()
            ->whereIn('role', self::STAFF_ROLES)
            ->whereHas('devices', fn ($q) => $q->active()->hasFcmToken())
            ->get();
    }

    /**
     * @param  Collection<int, UserDevice>  $devices
     * @return array<string, mixed>
     */
    private function dispatchToDevices($devices, string $type, string $screen, string $title, string $body, array $extra, array $platformOptions): array
    {
        $result = ['sent' => 0, 'deactivated' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($devices as $device) {
            $outcome = $this->sendToDevice($device, $type, $screen, $title, $body, $extra, $platformOptions);

            $result[$outcome['status']]++;
        }

        return $result;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function orderCopy(OnlineOrder $order, string $event): ?array
    {
        $orderData = [
            'order_id' => (string) $order->id,
            'order_number' => $order->order_number,
            'order_status' => $order->status,
        ];

        return match ($event) {
            'new' => [
                'type' => 'order.new',
                'screen' => 'order',
                'title' => 'New Order',
                'body' => 'New order '.$order->order_number.' — TZS '.$this->money($order->total),
                'extra' => $orderData,
            ],
            'accepted' => [
                'type' => 'order.accepted',
                'screen' => 'order',
                'title' => 'Order Accepted',
                'body' => 'Order '.$order->order_number.' was accepted by '.(string) ($order->rider?->name ?? 'a rider'),
                'extra' => $orderData,
            ],
            'dispatched' => [
                'type' => 'order.dispatched',
                'screen' => 'order',
                'title' => 'Order Dispatched',
                'body' => 'Order '.$order->order_number.' is out for delivery.',
                'extra' => $orderData,
            ],
            'delivered' => [
                'type' => 'order.delivered',
                'screen' => 'order',
                'title' => 'Order Delivered',
                'body' => 'Order '.$order->order_number.' has been delivered.',
                'extra' => $orderData,
            ],
            default => null,
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    private function paymentCopy(OnlineOrder $order, string $event): ?array
    {
        $orderData = [
            'order_id' => (string) $order->id,
            'order_number' => $order->order_number,
            'payment_status' => $order->payment_status,
        ];

        return match ($event) {
            'success' => [
                'type' => 'payment.success',
                'screen' => 'order',
                'title' => 'Payment Successful',
                'body' => 'Payment of TZS '.$this->money($order->total).' for order '.$order->order_number.' was received.',
                'extra' => $orderData,
            ],
            'failed' => [
                'type' => 'payment.failed',
                'screen' => 'order',
                'title' => 'Payment Failed',
                'body' => 'Payment for order '.$order->order_number.' failed. Please try again.',
                'extra' => $orderData,
            ],
            default => null,
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    private function tripCopy(TrackingSession $session, string $event): ?array
    {
        $order = $session->order;

        $tripData = [
            'tracking_session_id' => (string) $session->id,
            'order_id' => (string) ($order->id ?? ''),
            'order_number' => (string) ($order->order_number ?? ''),
            'trip_status' => $session->status,
        ];

        return match ($event) {
            'accepted' => [
                'type' => 'trip.accepted',
                'screen' => 'trip',
                'title' => 'Trip Accepted',
                'body' => 'Your rider has accepted your order and is preparing for delivery.',
                'extra' => $tripData,
            ],
            TrackingSession::STATUS_DRIVER_ARRIVING => [
                'type' => 'trip.driver_arriving',
                'screen' => 'trip',
                'title' => 'Rider on the Way',
                'body' => 'Your rider is on the way to pick up your order.',
                'extra' => $tripData,
            ],
            TrackingSession::STATUS_DRIVER_ARRIVED => [
                'type' => 'trip.driver_arrived',
                'screen' => 'trip',
                'title' => 'Rider Arrived',
                'body' => 'Your rider has arrived at the store.',
                'extra' => $tripData,
            ],
            TrackingSession::STATUS_TRIP_STARTED => [
                'type' => 'trip.started',
                'screen' => 'trip',
                'title' => 'Order Dispatched',
                'body' => 'Your order is on its way for delivery.',
                'extra' => $tripData,
            ],
            TrackingSession::STATUS_TRIP_IN_PROGRESS => [
                'type' => 'trip.in_progress',
                'screen' => 'trip',
                'title' => 'Delivery in Progress',
                'body' => 'Your order is being delivered. Track your rider live.',
                'extra' => $tripData,
            ],
            TrackingSession::STATUS_TRIP_COMPLETED => [
                'type' => 'trip.completed',
                'screen' => 'trip',
                'title' => 'Order Delivered',
                'body' => 'Your order has been delivered. Enjoy!',
                'extra' => $tripData,
            ],
            TrackingSession::STATUS_CANCELLED => [
                'type' => 'trip.cancelled',
                'screen' => 'trip',
                'title' => 'Trip Cancelled',
                'body' => 'Your delivery trip was cancelled. Contact support for help.',
                'extra' => $tripData,
            ],
            default => null,
        };
    }

    private function money(float|int|null $amount): string
    {
        return number_format((float) $amount, 0);
    }
}
