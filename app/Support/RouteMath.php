<?php

namespace App\Support;

class RouteMath
{
    /**
     * Distance from a point to a line segment defined by $a and $b (meters).
     */
    public static function distanceToSegment(
        float $pLat,
        float $pLng,
        float $aLat,
        float $aLng,
        float $bLat,
        float $bLng,
    ): float {
        $dx = $bLng - $aLng;
        $dy = $bLat - $aLat;

        $lengthSq = $dx * $dx + $dy * $dy;
        if ($lengthSq < 1e-12) {
            return self::haversine($pLat, $pLng, $aLat, $aLng);
        }

        $t = max(0, min(1, (($pLng - $aLng) * $dx + ($pLat - $aLat) * $dy) / $lengthSq));

        $projLat = $aLat + $t * $dy;
        $projLng = $aLng + $t * $dx;

        return self::haversine($pLat, $pLng, $projLat, $projLng);
    }

    /**
     * Maximum perpendicular distance from $point to the $polyline (array of [lat, lng]).
     * Used to detect whether the driver has deviated significantly from the route.
     */
    public static function maxDistanceToPolyline(array $polyline, array $point): float
    {
        if (count($polyline) < 2) {
            return PHP_FLOAT_MAX;
        }

        $max = 0.0;
        for ($i = 0; $i < count($polyline) - 1; $i++) {
            $d = self::distanceToSegment(
                $point[0],
                $point[1],
                $polyline[$i][0],
                $polyline[$i][1],
                $polyline[$i + 1][0],
                $polyline[$i + 1][1],
            );
            if ($d > $max) {
                $max = $d;
            }
        }

        return $max;
    }

    /**
     * Remaining distance (meters) along the polyline from the driver's current position to the end.
     * Steps along the polyline, finds the segment the driver is nearest to, then sums the rest.
     */
    public static function remainingDistanceAlongPolyline(array $polyline, array $point): float
    {
        if (count($polyline) < 2) {
            return 0.0;
        }

        // Cumulative segment lengths
        $segLengths = [];
        $total = 0.0;
        for ($i = 0; $i < count($polyline) - 1; $i++) {
            $len = self::haversine(
                $polyline[$i][0], $polyline[$i][1],
                $polyline[$i + 1][0], $polyline[$i + 1][1],
            );
            $segLengths[] = $len;
            $total += $len;
        }

        // Find nearest segment
        $bestIdx = 0;
        $bestDist = PHP_FLOAT_MAX;
        for ($i = 0; $i < count($polyline) - 1; $i++) {
            $d = self::distanceToSegment(
                $point[0], $point[1],
                $polyline[$i][0], $polyline[$i][1],
                $polyline[$i + 1][0], $polyline[$i + 1][1],
            );
            if ($d < $bestDist) {
                $bestDist = $d;
                $bestIdx = $i;
            }
        }

        // Distance from driver to the start of the best segment (approximation)
        $pre = self::haversine($point[0], $point[1], $polyline[$bestIdx][0], $polyline[$bestIdx][1]);

        // Sum of remaining segments from bestIdx onwards
        $remaining = 0.0;
        for ($i = $bestIdx; $i < count($segLengths); $i++) {
            $remaining += $segLengths[$i];
        }

        // Subtract the already-covered portion within the best segment (clamped)
        $consumed = min($segLengths[$bestIdx] ?? 0, max(0, $pre));

        return max(0, $remaining - $consumed);
    }

    private static function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        return Geo::haversine($lat1, $lng1, $lat2, $lng2);
    }
}
