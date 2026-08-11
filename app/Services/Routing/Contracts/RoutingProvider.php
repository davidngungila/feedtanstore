<?php

namespace App\Services\Routing\Contracts;

use App\Services\Routing\RouteResult;

interface RoutingProvider
{
    /**
     * Compute a driving route from $from [lat, lng] to $to [lat, lng].
     * Returns null when the route cannot be computed (no key / API error).
     */
    public function getRoute(array $from, array $to): ?RouteResult;

    public function name(): string;
}
