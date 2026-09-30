<?php

namespace App\Services;

use App\Models\CashDrawerSession;
use App\Models\CommunicationProfile;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;

/**
 * Shared queries behind the nightly management-alerts email and the
 * instant threshold alerts (alerts:check). Thresholds:
 *  - stock level  -> per-product reorder_level ("if level reach...")
 *  - expiry       -> 7 / 30 day windows
 *  - discounts    -> >= 25% of subtotal flagged as unusual
 */
class InventoryAlertService
{
    public const UNUSUAL_DISCOUNT_RATE = 0.25;

    /**
     * Low-stock products: quantity at/below reorder level.
     * When reorder level is not set (null/0), a default threshold of 5 applies.
     */
    public const DEFAULT_REORDER_LEVEL = 5;

    public static function lowStock(?int $limit = null)
    {
        $q = Product::with(['category', 'brand'])
            ->where('quantity', '>', 0)
            ->whereRaw('quantity <= COALESCE(NULLIF(reorder_level, 0), ?)', [self::DEFAULT_REORDER_LEVEL])
            ->orderBy('quantity')
            ->orderBy('name');
        return $limit ? $q->limit($limit)->get() : $q->get();
    }

    public static function outOfStock(?int $limit = null)
    {
        $q = Product::with(['category', 'brand'])
            ->where('quantity', '<=', 0)
            ->orderBy('name');
        return $limit ? $q->limit($limit)->get() : $q->get();
    }

    public static function expired(?int $limit = null)
    {
        $q = Product::with(['category', 'brand'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', today())
            ->orderBy('expiry_date');
        return $limit ? $q->limit($limit)->get() : $q->get();
    }

    public static function expiringWithin(int $days, ?int $limit = null)
    {
        $q = Product::with(['category', 'brand'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', today())
            ->where('expiry_date', '<=', today()->addDays($days))
            ->orderBy('expiry_date');
        return $limit ? $q->limit($limit)->get() : $q->get();
    }

    /** Completed sales of a day whose discount rate looks unusual. */
    public static function unusualDiscounts(string $date, float $rate = self::UNUSUAL_DISCOUNT_RATE, int $limit = 10)
    {
        return Sale::with(['customer', 'user'])
            ->whereDate('created_at', $date)
            ->where('discount', '>', 0)
            ->get()
            ->filter(fn ($s) => (float) $s->subtotal > 0 && ((float) $s->discount / (float) $s->subtotal) >= $rate)
            ->sortByDesc('discount')
            ->take($limit)
            ->values();
    }

    public static function cancelledCount(string $date): int
    {
        return Sale::onlyTrashed()->whereDate('created_at', $date)->count();
    }

    /** Drawer sessions closed on a day whose counted cash differs from expected. */
    public static function cashDiscrepancies(string $date)
    {
        return CashDrawerSession::with('user')
            ->whereDate('closed_at', $date)
            ->where('difference', '!=', 0)
            ->orderBy('closed_at', 'desc')
            ->get();
    }

    public static function overview(string $date): array
    {
        $products = Product::query();
        return [
            'totalProducts' => (clone $products)->count(),
            'totalStockValue' => (float) Product::sum(\DB::raw('quantity * cost_price')),
            'productsSold' => (int) SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->whereNull('sales.deleted_at')
                ->whereDate('sales.created_at', $date)
                ->distinct('sale_items.product_id')
                ->count('sale_items.product_id'),
            'itemsSold' => (int) SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->whereNull('sales.deleted_at')
                ->whereDate('sales.created_at', $date)
                ->sum('sale_items.quantity'),
            'lowStockCount' => (int) Product::where('quantity', '>', 0)->whereRaw('quantity <= COALESCE(NULLIF(reorder_level, 0), ?)', [self::DEFAULT_REORDER_LEVEL])->count(),
            'outOfStockCount' => (int) Product::where('quantity', '<=', 0)->count(),
            'expiredCount' => (int) Product::whereNotNull('expiry_date')->where('expiry_date', '<', today())->count(),
        ];
    }

    /**
     * Same sending path as purchase-order / online-order emails:
     * active email CommunicationProfile's SMTP, else default smtp mailer.
     */
    public static function resolveMailer(): string
    {
        try {
            $emailProfile = CommunicationProfile::where('type', 'email')->where('is_active', true)->first();
            if (!$emailProfile) {
                \Log::warning('No active email CommunicationProfile; alert email uses default mailer.');
                return 'smtp';
            }

            config([
                'mail.mailers.test_smtp' => [
                    'transport' => 'smtp',
                    'host' => $emailProfile->smtp_host,
                    'port' => $emailProfile->smtp_port,
                    'encryption' => $emailProfile->smtp_encryption,
                    'username' => $emailProfile->smtp_username,
                    'password' => $emailProfile->smtp_password,
                    'timeout' => 30,
                    'local_domain' => null,
                ],
                'mail.from' => [
                    'address' => $emailProfile->email_from_address,
                    'name' => $emailProfile->email_from_name,
                ],
            ]);

            return 'test_smtp';
        } catch (\Throwable $e) {
            \Log::error('Failed to configure alert mailer: ' . $e->getMessage());
            return 'smtp';
        }
    }

    public static function recipients(?string $override = null): array
    {
        return collect(explode(',', (string) ($override ?: config('mail.nightly_to'))))
            ->map(fn ($e) => trim($e))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->values()
            ->all();
    }
}
