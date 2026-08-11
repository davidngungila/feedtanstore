<?php

namespace App\Events;

use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TrackingSession $session,
        public string $status,
        public ?User $actor = null,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tracking.session.'.$this->session->id)];
    }

    public function broadcastAs(): string
    {
        return 'trip.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'trip_id' => $this->session->id,
            'status' => $this->status,
            'at' => now()->toIso8601String(),
            'actor' => $this->actor?->name,
        ];
    }
}
