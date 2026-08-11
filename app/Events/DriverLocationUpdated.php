<?php

namespace App\Events;

use App\Models\RiderLocation;
use App\Models\TrackingSession;
use App\Services\Tracking\TrackingService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TrackingSession $session,
        public RiderLocation $location,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tracking.session.'.$this->session->id)];
    }

    public function broadcastAs(): string
    {
        return 'driver.location.updated';
    }

    public function broadcastWith(): array
    {
        return app(TrackingService::class)->locationPayload($this->session, $this->location);
    }
}
