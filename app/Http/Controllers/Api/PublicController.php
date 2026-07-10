<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function termsAndPolicies()
    {
        $settings = StoreSetting::first();
        return response()->json([
            'terms_of_service' => $settings->terms_of_service,
            'privacy_policy' => $settings->privacy_policy,
            'rider_terms' => $settings->rider_terms,
            'rider_privacy_policy' => $settings->rider_privacy_policy,
        ]);
    }

    public function riderSupport()
    {
        $settings = StoreSetting::first();
        return response()->json([
            'support_email' => $settings->store_email,
            'support_phone' => $settings->store_phone,
            'support_address' => $settings->store_address,
        ]);
    }
}
