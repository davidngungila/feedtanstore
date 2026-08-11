<?php

namespace App\Support;

class Geo
{
    private const EARTH_RADIUS_METERS = 6371000.0;

    public static function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * self::EARTH_RADIUS_METERS * asin(min(1.0, sqrt($a)));
    }

    /**
     * Initial bearing from point A to point B, in degrees (0-360, clockwise from north).
     */
    public static function bearing(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $dLng = deg2rad($lng2 - $lng1);

        $y = sin($dLng) * cos($phi2);
        $x = cos($phi1) * sin($phi2) - sin($phi1) * cos($phi2) * cos($dLng);

        return (rad2deg(atan2($y, $x)) + 360) % 360;
    }

    /**
     * Great-circle midpoint (used to center maps when a route is unavailable).
     */
    public static function midpoint(float $lat1, float $lng1, float $lat2, float $lng2): array
    {
        $phi1 = deg2rad($lat1);
        $lambda1 = deg2rad($lng1);
        $phi2 = deg2rad($lat2);
        $lambda2 = deg2rad($lng2);

        $bx = cos($phi2) * cos($lambda2 - $lambda1);
        $by = cos($phi2) * sin($lambda2 - $lambda1);

        $phi3 = atan2(sin($phi1) + sin($phi2), sqrt((cos($phi1) + $bx) ** 2 + $by ** 2));
        $lambda3 = $lambda1 + atan2($by, cos($phi1) + $bx);

        return [rad2deg($phi3), rad2deg($lambda3)];
    }
}
