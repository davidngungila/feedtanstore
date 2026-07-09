<?php

namespace App\Jobs;

use App\Models\OnlineOrder;
use App\Services\FeedtanEcommercePaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InitiateOnlineOrderPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $phoneNumber;

    /**
     * Create a new job instance.
     */
    public function __construct(OnlineOrder $order, ?string $phoneNumber = null)
    {
        $this->order = $order;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(FeedtanEcommercePaymentService $paymentService): void
    {
        try {
            // Try to initiate payment
            $paymentResponse = $paymentService->initiatePayment($this->buildPaymentPayload($this->order, $this->phoneNumber));

            if (isset($paymentResponse['success']) && $paymentResponse['success']) {
                $this->syncOrderPaymentState($this->order, $paymentResponse['data'] ?? [], 'Payment initiated via public checkout (background)');
            }
        } catch (\Exception $e) {
            Log::error('FeedTan e-commerce payment initiation (background) failed: ' . $e->getMessage(), ['order_id' => $this->order->id]);
        }
    }

    private function buildPaymentPayload(OnlineOrder $order, ?string $phoneNumber = null): array
    {
        $order->loadMissing(['items.product']);
        $gatewayOrderReference = $this->ensureGatewayOrderReference($order);

        $cartItemsForMetadata = $order->items->map(function ($item) {
            $name = $item->product ? $item->product->name : 'Item';
            return [
                'name' => $name,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ];
        })->values()->all();

        return [
            'amount' => (float) $order->total,
            'phone_number' => $phoneNumber ?: $this->normalizePhoneNumber($order->customer_phone),
            'payer_name' => $order->customer_name,
            'description' => "Order {$order->order_number} - Shopping Cart",
            'order_reference' => $gatewayOrderReference,
            'email' => $order->customer_email,
            'callback_url' => route('api.shop.payments.feedtan.callback'),
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'items' => $cartItemsForMetadata,
            ],
        ];
    }

    private function normalizePhoneNumber(?string $phoneNumber): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phoneNumber);
        if (!$digits) {
            return null;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            $digits = '255' . substr($digits, 1);
        } elseif (strlen($digits) === 9 && str_starts_with($digits, '7')) {
            $digits = '255' . $digits;
        }

        if (!str_starts_with($digits, '255') || strlen($digits) !== 12) {
            return null;
        }

        return $digits;
    }

    private function ensureGatewayOrderReference(OnlineOrder $order): string
    {
        $currentReference = strtoupper((string) $order->payment_order_reference);
        if ($currentReference !== '' && preg_match('/^[A-Z0-9]+$/', $currentReference)) {
            return $currentReference;
        }

        $generatedReference = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $order->order_number));
        if ($generatedReference === '') {
            $generatedReference = 'ORD' . $order->id . strtoupper(substr(md5((string) $order->id), 0, 8));
        }

        if ($order->payment_order_reference !== $generatedReference) {
            $order->forceFill([
                'payment_order_reference' => $generatedReference,
            ])->save();
        }

        return $generatedReference;
    }

    private function syncOrderPaymentState(OnlineOrder $order, array $paymentData, string $historyNotePrefix = 'Payment sync'): void
    {
        $gatewayStatus = strtoupper((string) ($paymentData['status'] ?? $paymentData['clickpesa_status'] ?? ''));
        $isPaid = (bool) ($paymentData['is_paid'] ?? false);

        $updates = [
            'payment_transaction_id' => $paymentData['transaction_id'] ?? $order->payment_transaction_id,
            'payment_order_reference' => $paymentData['order_reference'] ?? $this->ensureGatewayOrderReference($order),
            'clickpesa_status' => $gatewayStatus !== '' ? $gatewayStatus : $order->clickpesa_status,
        ];

        $resolvedPaymentStatus = $order->payment_status;
        if ($isPaid || in_array($gatewayStatus, ['SUCCESS', 'SETTLED'], true)) {
            $resolvedPaymentStatus = 'paid';
        } elseif ($order->payment_status === 'paid') {
            $resolvedPaymentStatus = 'paid';
        } elseif (in_array($gatewayStatus, ['FAILED', 'DECLINED', 'CANCELLED'], true)) {
            $resolvedPaymentStatus = 'failed';
        } elseif ($gatewayStatus !== '' && $order->payment_status !== 'paid') {
            $resolvedPaymentStatus = 'pending';
        }

        $updates['payment_status'] = $resolvedPaymentStatus;

        $paymentStatusChanged = $resolvedPaymentStatus !== $order->payment_status;
        $gatewayStatusChanged = ($updates['clickpesa_status'] ?? null) !== $order->clickpesa_status;
        $transactionChanged = ($updates['payment_transaction_id'] ?? null) !== $order->payment_transaction_id;

        $order->update($updates);

        if ($paymentStatusChanged || $gatewayStatusChanged || $transactionChanged) {
            $notes = [];
            if ($gatewayStatusChanged && $updates['clickpesa_status']) {
                $notes[] = 'Gateway status: ' . $updates['clickpesa_status'];
            }
            if ($paymentStatusChanged) {
                $notes[] = 'Payment status changed to ' . $resolvedPaymentStatus;
            }
            if ($transactionChanged && $updates['payment_transaction_id']) {
                $notes[] = 'Transaction ID: ' . $updates['payment_transaction_id'];
            }

            \App\Models\OnlineOrderStatusHistory::create([
                'online_order_id' => $order->id,
                'status' => $order->status,
                'payment_status' => $resolvedPaymentStatus,
                'notes' => trim($historyNotePrefix . ($notes ? ' | ' . implode(' | ', $notes) : '')),
            ]);
        }
    }
}
