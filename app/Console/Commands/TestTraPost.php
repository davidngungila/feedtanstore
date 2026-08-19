<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Services\TraVfdService;
use Illuminate\Console\Command;

class TestTraPost extends Command
{
    protected $signature = 'tra:test-post {sale_id=1}';
    protected $description = 'Test posting a sale receipt to TRA VFD API';

    public function handle(): int
    {
        $saleId = $this->argument('sale_id');
        $sale = Sale::withTrashed()->with(['customer', 'user', 'items.product'])->find($saleId);

        if (!$sale) {
            $this->error("Sale #{$saleId} not found.");
            return 1;
        }

        $this->info("=== TRA VFD Test Post ===");
        $this->info("Sale: {$sale->invoice_number}");
        $this->info("Total: {$sale->total}");
        $this->info("Items: {$sale->items->count()}");
        $this->line("");

        // Show XML that will be sent
        $traService = new TraVfdService();

        if (!$traService->isConfigured()) {
            $this->error("TRA VFD API is not configured. Run: php artisan db:seed --class=TraVfdSeeder");
            return 1;
        }

        $this->info("Posting to TRA...");
        $this->line("");

        $result = $traService->postReceipt($sale);

        if ($result['success']) {
            $this->info("SUCCESS!");
            $this->info("Receipt Number: " . ($result['receipt_number'] ?? 'N/A'));
            $this->info("Verification Link: " . ($result['verification_link'] ?? 'N/A'));
            $this->info("QR Code: " . ($result['qr_code'] ?? 'N/A'));
        } else {
            $this->error("FAILED: " . ($result['error'] ?? 'Unknown error'));
            if (isset($result['command_code'])) {
                $this->error("TRA Error Code: {$result['command_code']}");
            }
        }

        if (isset($result['raw_response'])) {
            $this->line("");
            $this->line("Raw Response:");
            $this->line($result['raw_response']);
        }

        return $result['success'] ? 0 : 1;
    }
}
