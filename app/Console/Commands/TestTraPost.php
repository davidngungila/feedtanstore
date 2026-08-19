<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\StoreSetting;
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

        $settings = StoreSetting::first();
        $taxPercent = (int) ($settings->tax_rate ?? 18);
        if ($taxPercent <= 0) $taxPercent = 18;

        $this->info("=== TRA VFD Test Post (v" . TraVfdService::VERSION . ") ===");
        $this->info("Sale: {$sale->invoice_number}");
        $this->info("Tax Rate: {$taxPercent}%");
        $this->info("Tax Enabled: " . ($settings->tax_enabled ? 'Yes' : 'No'));
        $this->info("GC: " . ($settings->tra_gc ?? 1) . " | DC: " . ($settings->tra_dc ?? 1) . " | ZNUM: " . ($settings->tra_znum ?? date('Ymd')));
        $this->line("");

        $traService = new TraVfdService();

        if (!$traService->isConfigured()) {
            $this->error("TRA VFD API is not configured. Run: php artisan db:seed --class=TraVfdSeeder");
            return 1;
        }

        // Show per-item VAT breakdown
        $this->info("=== Per-Item VAT Breakdown ===");
        $totalVat = 0.00;
        $totalGross = 0.00;
        foreach ($sale->items as $item) {
            $product = $item->product;
            $taxCode = (int) ($product->tax_code ?? 1);
            $amt = round((float) $item->total, 2);
            $itemVat = 0.00;
            if ($taxCode == 1) {
                $itemVat = round($amt * $taxPercent / (100 + $taxPercent), 2);
            }
            $totalVat += $itemVat;
            $totalGross += $amt;
            $this->line("  Item: {$product->name} | TaxCode: {$taxCode} | AMT: {$amt} | VAT: {$itemVat}");
        }
        $this->info("  Total VAT (sum): " . round($totalVat, 2));
        $this->info("  Total Gross (sum): " . round($totalGross, 2));
        $this->line("");

        $this->info("Posting to TRA...");
        $this->line("");

        $xml = $traService->buildXml($sale);
        $this->line("=== XML Payload ===");
        $this->line($xml);
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
