<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FeedtanEcommercePaymentService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('feedtan_ecommerce.base_url'), '/');
    }

    public function initiatePayment(array $data): array
    {
        $response = Http::timeout(15)
            ->acceptJson()
            ->post($this->baseUrl . '/payments/initiate', $data);

        return $this->normalizeResponse($response);
    }

    public function checkPaymentStatus(string $orderReference): array
    {
        $response = Http::timeout(15)
            ->acceptJson()
            ->get($this->baseUrl . '/payments/status/' . urlencode($orderReference));

        return $this->normalizeResponse($response);
    }

    protected function normalizeResponse($response): array
    {
        $payload = $response->json();

        if (is_array($payload)) {
            return $payload;
        }

        return [
            'success' => $response->successful(),
            'message' => trim((string) $response->body()),
        ];
    }
}
