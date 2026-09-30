<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportController;
use App\Mail\NightlyReportsMail;
use App\Services\InventoryAlertService;
use Dompdf\Dompdf;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendNightlyReports extends Command
{
    protected $signature = 'reports:nightly {--date= : Report date (Y-m-d), defaults to today} {--to= : Comma-separated test recipient(s), overrides mail.nightly_to}';
    protected $description = 'Email the daily sales + full inventory PDFs to admins (scheduled 23:59 daily)';

    public function handle(): int
    {
        $date = $this->option('date') ?: today()->toDateString();

        $controller = app(ReportController::class);

        // Reuse the exact same data as the on-screen/PDF report actions
        $salesData = $controller->dailySales(Request::create('/', 'GET', ['date' => $date]))->getData();
        $stockData = $controller->currentStock()->getData();

        $salesPdf = $this->renderPdf('reports.sales.daily-pdf', $salesData);
        $stockPdf = $this->renderPdf('reports.inventory.current-stock-pdf', $stockData);

        $summary = [
            'totalSales' => (float) ($salesData['totalSales'] ?? 0),
            'transactionCount' => (int) ($salesData['transactionCount'] ?? 0),
            'itemsSold' => (int) ($salesData['itemsSold'] ?? 0),
            'totalStockValue' => (float) ($stockData['totalStockValue'] ?? 0),
        ];

        // Management / expiration / overview sections for the email body
        $overview = InventoryAlertService::overview($date);
        $lowStock = InventoryAlertService::lowStock(15);
        $outOfStock = InventoryAlertService::outOfStock(15);
        $expired = InventoryAlertService::expired(15);
        $expiring7 = InventoryAlertService::expiringWithin(7, 15);
        $expiring30 = InventoryAlertService::expiringWithin(30, 15);
        $unusualDiscounts = InventoryAlertService::unusualDiscounts($date);
        $cancelledCount = InventoryAlertService::cancelledCount($date);
        $cashDiscrepancies = InventoryAlertService::cashDiscrepancies($date);

        $alerts = compact('overview', 'lowStock', 'outOfStock', 'expired', 'expiring7', 'expiring30', 'unusualDiscounts', 'cancelledCount', 'cashDiscrepancies');

        $recipients = InventoryAlertService::recipients($this->option('to'));

        if (empty($recipients)) {
            $this->error('No valid nightly report recipients configured (mail.nightly_to).');
            return self::FAILURE;
        }

        // Same sending path as purchase-order / online-order emails:
        // use the active email CommunicationProfile's SMTP settings.
        $mailer = InventoryAlertService::resolveMailer();

        try {
            Mail::mailer($mailer)->to($recipients)->send(new NightlyReportsMail($date, $salesPdf, $stockPdf, $summary, $alerts));
        } catch (\Throwable $e) {
            \Log::error('Nightly sales+inventory report failed to send: ' . $e->getMessage());
            $this->error('Report built but email failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        \Log::info('Nightly sales+inventory report emailed', ['date' => $date, 'via' => $mailer, 'to' => $recipients]);
        $this->info('Nightly report for ' . $date . ' sent via [' . $mailer . '] to: ' . implode(', ', $recipients));

        return self::SUCCESS;
    }

    protected function renderPdf(string $view, array $data): string
    {
        $pdf = new Dompdf();
        $pdf->loadHtml(view($view, $data)->render());
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        return $pdf->output();
    }
}
