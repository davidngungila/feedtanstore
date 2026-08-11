<?php

namespace App\Events;

use App\Models\TrackingSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public TrackingSession $session)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tracking.session.'.$this->session->id)];
    }

    public function broadcastAs(): string
    {
        return 'trip.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'trip_id' => $this->session->id,
            'status' => TrackingSession::STATUS_TRIP_COMPLETED,
            'at' => now()->toIso8601String(),
        ];
    }
}
