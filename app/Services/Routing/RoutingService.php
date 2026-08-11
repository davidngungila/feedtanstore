<?php

namespace App\Services\Routing;

use App\Support\Geo;
use App\Services\Routing\Contracts\RoutingProvider;

/**
 * Route calculation service with a swappable provider and a guaranteed
 * straight-line fallback so tracking never breaks when the routing API is down.
 */
class RoutingService
{
    private const FALLBACK_SPEED_MS = 8.33; // ~30 km/h average urban driving

    public function __construct(
        private readonly RoutingProvider $provider,
    ) {
    }

    public function providerName(): string
    {
        return $this->provider->name();
    }

    public function getRoute(array $from, array $to): RouteResult
    {
        $result = $this->provider->getRoute($from, $to);

        if ($result !== null) {
            return $result;
        }

        return $this->straightLineFallback($from, $to);
    }

    private function straightLineFallback(array $from, array $to): RouteResult
    {
        $distance = Geo::haversine($from[0], $from[1], $to[0], $to[1]);
        $duration = $distance > 0 ? $distance / self::FALLBACK_SPEED_MS : 0;

        return new RouteResult(
            distanceMeters: $distance,
            durationSeconds: $duration,
            polyline: [
                [(float) $from[0], (float) $from[1]],
                [(float) $to[0], (float) $to[1]],
            ],
            provider: 'straight-line-fallback',
        );
    }
}
