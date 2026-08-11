<?php

use App\Models\TrackingSession;
use Illuminate\Support\Facades\Broadcast;

/**
 * Private channel authorization for live trip tracking.
 *
 * Only the assigned rider and trusted staff (admin / manager / marketing officer)
 * may subscribe. This prevents clients from subscribing to arbitrary trips.
 *
 * Customer accounts: when online orders are linked to a customer user account
 * in the future, add a check here comparing the order customer's user_id.
 */
Broadcast::channel('tracking.session.{sessionId}', function ($user, $sessionId) {
    $session = TrackingSession::with('rider')->find($sessionId);

    if (! $session) {
        return false;
    }

    // Assigned rider
    if ($session->rider && $session->rider->user_id === $user->id) {
        return true;
    }

    // Trusted staff (marketing officer tracks deliveries in real time)
    if (in_array($user->role, ['admin', 'manager', 'marketing_officer'], true)) {
        return true;
    }

    return false;
});
