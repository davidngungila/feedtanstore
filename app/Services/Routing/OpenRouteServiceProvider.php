<?php

namespace App\Services\Routing;

use App\Models\StoreSetting;
use App\Services\Routing\Contracts\RoutingProvider;
use Illuminate\Support\Facades\Http;

class OpenRouteServiceProvider implements RoutingProvider
{
    public function getRoute(array $from, array $to): ?RouteResult
    {
        $key = StoreSetting::first()?->openrouteservice_api_key;

        if (! $key) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $key,
                'Content-Type' => 'application/json',
            ])->timeout(8)->post('https://api.openrouteservice.org/v2/directions/driving-car/geojson', [
                'coordinates' => [
                    [$from[1], $from[0]], // ORS expects [longitude, latitude]
                    [$to[1], $to[0]],
                ],
            ]);

            if (! $response->successful()) {
                return null;
            }

            $feature = $response->json('features.0');

            if (! $feature) {
                return null;
            }

            $coords = $feature['geometry']['coordinates'] ?? [];
            $polyline = array_map(
                fn (array $c) => [(float) $c[1], (float) $c[0]], // [lng, lat] -> [lat, lng]
                $coords,
            );

            $segment = $feature['properties']['segments'][0] ?? [];
            $distance = (float) ($segment['distance'] ?? 0);
            $duration = (float) ($segment['duration'] ?? 0);

            // Fallback to summary when segments are missing
            if ($distance === 0.0 && isset($feature['properties']['summary']['distance'])) {
                $distance = (float) $feature['properties']['summary']['distance'];
                $duration = (float) $feature['properties']['summary']['duration'];
            }

            if ($distance <= 0 || count($polyline) < 2) {
                return null;
            }

            return new RouteResult(
                distanceMeters: $distance,
                durationSeconds: $duration,
                polyline: $polyline,
                steps: $feature['properties']['segments'][0]['steps'] ?? [],
                provider: 'openrouteservice',
            );
        } catch (\Throwable) {
            return null;
        }
    }

    public function name(): string
    {
        return 'openrouteservice';
    }
}
