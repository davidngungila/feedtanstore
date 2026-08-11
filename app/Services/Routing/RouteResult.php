<?php

namespace App\Services\Routing;

class RouteResult
{
    /**
     * @param  array<int, array{0: float, 1: float}>  $polyline  list of [lat, lng] points
     */
    public function __construct(
        public readonly float $distanceMeters,
        public readonly float $durationSeconds,
        public readonly array $polyline,
        public readonly array $steps = [],
        public readonly string $provider = 'unknown',
    ) {
    }

    public function toArray(): array
    {
        return [
            'distance_meters' => round($this->distanceMeters),
            'duration_seconds' => (int) round($this->durationSeconds),
            'polyline' => array_map(
                fn (array $p) => [(float) $p[0], (float) $p[1]],
                $this->polyline,
            ),
            'provider' => $this->provider,
        ];
    }
}
