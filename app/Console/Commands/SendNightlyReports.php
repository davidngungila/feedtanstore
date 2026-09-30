<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportController;
use App\Mail\NightlyReportsMail;
use Dompdf\Dompdf;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendNightlyReports extends Command
{
    protected $signature = 'reports:nightly {--date= : Report date (Y-m-d), defaults to today}';
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

        $recipients = collect(explode(',', (string) config('mail.nightly_to')))
            ->map(fn ($e) => trim($e))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->values()
            ->all();

        if (empty($recipients)) {
            $this->error('No valid nightly report recipients configured (mail.nightly_to).');
            return self::FAILURE;
        }

        Mail::to($recipients)->send(new NightlyReportsMail($date, $salesPdf, $stockPdf));

        \Log::info('Nightly sales+inventory report emailed', ['date' => $date, 'to' => $recipients]);
        $this->info('Nightly report for ' . $date . ' sent to: ' . implode(', ', $recipients));

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
