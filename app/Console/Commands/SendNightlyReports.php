<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportController;
use App\Mail\NightlyReportsMail;
use App\Models\CommunicationProfile;
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

        $recipients = collect(explode(',', (string) ($this->option('to') ?: config('mail.nightly_to'))))
            ->map(fn ($e) => trim($e))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->values()
            ->all();

        if (empty($recipients)) {
            $this->error('No valid nightly report recipients configured (mail.nightly_to).');
            return self::FAILURE;
        }

        // Same sending path as purchase-order / online-order emails:
        // use the active email CommunicationProfile's SMTP settings.
        $mailer = $this->resolveMailer();

        Mail::mailer($mailer)->to($recipients)->send(new NightlyReportsMail($date, $salesPdf, $stockPdf));

        \Log::info('Nightly sales+inventory report emailed', ['date' => $date, 'via' => $mailer, 'to' => $recipients]);
        $this->info('Nightly report for ' . $date . ' sent via [' . $mailer . '] to: ' . implode(', ', $recipients));

        return self::SUCCESS;
    }

    /**
     * Configure the runtime test_smtp mailer from the active email
     * CommunicationProfile (purchase orders & online orders send this way).
     * Falls back to the default smtp mailer (.env) when no profile exists.
     */
    protected function resolveMailer(): string
    {
        try {
            $emailProfile = CommunicationProfile::where('type', 'email')->where('is_active', true)->first();
            if (!$emailProfile) {
                \Log::warning('No active email CommunicationProfile; nightly report uses default mailer.');
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
            \Log::error('Failed to configure nightly report mailer: ' . $e->getMessage());
            return 'smtp';
        }
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
