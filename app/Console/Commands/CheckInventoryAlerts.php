<?php

namespace App\Console\Commands;

use App\Mail\InventoryAlertMail;
use App\Models\CashDrawerSession;
use App\Models\Sale;
use App\Services\InventoryAlertService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class CheckInventoryAlerts extends Command
{
    protected $signature = 'alerts:check {--to= : Comma-separated test recipient(s), overrides mail.nightly_to} {--fresh : Ignore already-sent marks (for testing)}';
    protected $description = 'Send immediate email when stock/expiry/cash thresholds are breached (runs every few minutes)';

    public function handle(): int
    {
        $today = today()->toDateString();
        $fresh = (bool) $this->option('fresh');
        $new = ['lowStock' => collect(), 'outOfStock' => collect(), 'expired' => collect(), 'expiring7' => collect(), 'unusualDiscounts' => collect(), 'cashDiscrepancies' => collect()];

        // Products breaching reorder level / out of stock (alert once per product per day)
        foreach (InventoryAlertService::lowStock() as $p) {
            $key = "inv_alert:low:{$p->id}:{$today}";
            if ($fresh || !Cache::has($key)) {
                $new['lowStock']->push($p);
                Cache::put($key, true, now()->endOfDay());
            }
        }
        foreach (InventoryAlertService::outOfStock() as $p) {
            $key = "inv_alert:out:{$p->id}:{$today}";
            if ($fresh || !Cache::has($key)) {
                $new['outOfStock']->push($p);
                Cache::put($key, true, now()->endOfDay());
            }
        }

        // Expiry breaches (alert once per product per day)
        foreach (InventoryAlertService::expired() as $p) {
            $key = "inv_alert:expired:{$p->id}:{$today}";
            if ($fresh || !Cache::has($key)) {
                $new['expired']->push($p);
                Cache::put($key, true, now()->endOfDay());
            }
        }
        foreach (InventoryAlertService::expiringWithin(7) as $p) {
            $key = "inv_alert:exp7:{$p->id}:{$today}";
            if ($fresh || !Cache::has($key)) {
                $new['expiring7']->push($p);
                Cache::put($key, true, now()->endOfDay());
            }
        }

        // Unusual discounts in the last 10 minutes (alert once per sale)
        $recentUnusual = Sale::with(['customer', 'user'])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->where('discount', '>', 0)
            ->get()
            ->filter(fn ($s) => (float) $s->subtotal > 0 && ((float) $s->discount / (float) $s->subtotal) >= InventoryAlertService::UNUSUAL_DISCOUNT_RATE);
        foreach ($recentUnusual as $s) {
            $key = "inv_alert:discount:{$s->id}";
            if ($fresh || !Cache::has($key)) {
                $new['unusualDiscounts']->push($s);
                Cache::put($key, true, now()->addDays(7));
            }
        }

        // Cash discrepancies on sessions closed in the last 10 minutes (alert once per session)
        $recentDiscrepancies = CashDrawerSession::with('user')
            ->whereNotNull('closed_at')
            ->where('closed_at', '>=', now()->subMinutes(10))
            ->where('difference', '!=', 0)
            ->get();
        foreach ($recentDiscrepancies as $d) {
            $key = "inv_alert:drawer:{$d->id}";
            if ($fresh || !Cache::has($key)) {
                $new['cashDiscrepancies']->push($d);
                Cache::put($key, true, now()->addDays(7));
            }
        }

        $total = $new['lowStock']->count() + $new['outOfStock']->count() + $new['expired']->count()
            + $new['expiring7']->count() + $new['unusualDiscounts']->count() + $new['cashDiscrepancies']->count();

        if ($total === 0) {
            $this->info('No new threshold breaches.');
            return self::SUCCESS;
        }

        $recipients = InventoryAlertService::recipients($this->option('to'));
        if (empty($recipients)) {
            $this->error('No valid recipients configured (mail.nightly_to).');
            return self::FAILURE;
        }

        $mailer = InventoryAlertService::resolveMailer();
        try {
            Mail::mailer($mailer)->to($recipients)->send(new InventoryAlertMail($today, $new));
        } catch (\Throwable $e) {
            \Log::error('Instant inventory alert failed to send: ' . $e->getMessage());
            $this->error('Alert detected (' . $total . ' breach(es)) but email failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        \Log::warning('Instant inventory alert emailed', ['via' => $mailer, 'to' => $recipients, 'counts' => collect($new)->map->count()]);
        $this->info("Alert sent via [{$mailer}] ({$total} new breach(es)) to: " . implode(', ', $recipients));

        return self::SUCCESS;
    }
}
