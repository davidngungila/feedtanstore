<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClickPesaService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('clickpesa.base_url');
    }

    public function initiatePayment($data)
    {
        $response = Http::timeout(5)->post($this->baseUrl . '/payments/initiate', $data);
        return $response->json();
    }

    public function checkPaymentStatus($orderReference)
    {
        $response = Http::timeout(5)->get($this->baseUrl . '/payments/status/' . $orderReference);
        return $response->json();
    }
}
