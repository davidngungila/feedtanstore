<?php

namespace App\Services;

use App\Models\StoreSetting;

class VFDService
{
    private $port;
    private $baudRate;
    private $dataBits;
    private $stopBits;
    private $parity;
    private $isWindows;
    private $enabled;

    public function __construct()
    {
        $settings = StoreSetting::firstOrCreate();

        $this->enabled = $settings->vfd_enabled ?? false;
        $this->port = $settings->vfd_port ?? env('VFD_PORT', 'COM3');
        $this->baudRate = $settings->vfd_baud ?? env('VFD_BAUD', 9600);
        $this->dataBits = $settings->vfd_data_bits ?? env('VFD_DATA_BITS', 8);
        $this->stopBits = $settings->vfd_stop_bits ?? env('VFD_STOP_BITS', 1);
        $this->parity = $settings->vfd_parity ?? env('VFD_PARITY', 'none');
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public function displayWelcome()
    {
        $this->sendVFDMessage("WELCOME", "FEEDTAN STORE");
    }

    public function displayProduct($productName, $quantity, $price, $total)
    {
        $line1 = substr($productName, 0, 20);
        $line2 = "Qty: $quantity  TZS " . number_format($total, 0);
        $this->sendVFDMessage($line1, $line2);
    }

    public function displayPayment($total, $paid, $change, $paymentMethod)
    {
        $line1 = "TOTAL: TZS " . number_format($total, 0);
        $line2 = strtoupper($paymentMethod) . ": " . number_format($paid, 0);
        $this->sendVFDMessage($line1, $line2);
        usleep(200000);
        $this->sendVFDMessage("CHANGE: TZS " . number_format($change, 0), "");
    }

    public function displayThankYou()
    {
        $this->sendVFDMessage("THANK YOU", "COME AGAIN");
    }

    private function sendVFDMessage($line1, $line2 = '')
    {
        $logs = [];
        $logs[] = "VFD Config: Enabled=". ($this->enabled ? 'Yes' : 'No') . ", Port={$this->port}, Baud={$this->baudRate}";

        if (!$this->enabled) {
            \Log::info('VFD is disabled');
            $logs[] = 'VFD is disabled';
            return ['success' => false, 'logs' => $logs];
        }

        try {
            // Configure port
            if ($this->isWindows) {
                $this->configureWindowsPort($logs);
            } else {
                $this->configureLinuxPort($logs);
            }

            // Open port
            $handle = @fopen($this->port, 'w');
            if (!$handle) {
                $error = error_get_last();
                $logs[] = "ERROR: Cannot open port {$this->port}: " . ($error['message'] ?? 'Unknown');
                \Log::error('VFD port open failed', $error);
                return ['success' => false, 'logs' => $logs];
            }
            $logs[] = "SUCCESS: Port opened";

            // Common VFD initialization and display sequences
            $protocols = [
                // Protocol 1: ESC @ to init, CR+LF line endings
                'ESC @ Init' => function ($l1, $l2) {
                    return "\x1B@" . $l1 . "\r\n" . $l2 . "\r\n";
                },
                // Protocol 2: Form Feed to clear screen
                'Form Feed Init' => function ($l1, $l2) {
                    return "\x0C" . $l1 . "\r\n" . $l2 . "\r\n";
                },
                // Protocol 3: ESC @, then move cursor to home
                'ESC @ + Home' => function ($l1, $l2) {
                    return "\x1B@" . "\x1B[H" . $l1 . "\r\n" . $l2 . "\r\n";
                },
                // Protocol 4: ESC [2J to clear display
                'Clear Display (ESC [2J)' => function ($l1, $l2) {
                    return "\x1B[2J" . $l1 . "\r\n" . $l2 . "\r\n";
                },
                // Protocol 5: Common POS Display (like Bixolon)
                'POS Protocol' => function ($l1, $l2) {
                    return "\x1B@" . "\x1B" . "|" . "lA" . $l1 . "\r\n" . $l2 . "\r\n" . "\x0C";
                },
                // Protocol 6: Epson-like
                'Epson-like' => function ($l1, $l2) {
                    return "\x1B@" . "\x1B" . "c" . "\x03" . $l1 . "\r\n" . $l2 . "\r\n" . "\x0C";
                },
                // Protocol 7: Simple, no init codes
                'Simple No Init' => function ($l1, $l2) {
                    return $l1 . "\r\n" . $l2 . "\r\n";
                },
            ];

            foreach ($protocols as $name => $generator) {
                $message = $generator($line1, $line2);
                $bytesWritten = fwrite($handle, $message);
                fflush($handle);
                $logs[] = "Protocol $name: wrote $bytesWritten bytes";
                usleep(250000); // 0.25 seconds between attempts
            }

            fclose($handle);
            \Log::info('VFD sent successfully', $logs);

            return ['success' => true, 'logs' => $logs];
        } catch (\Exception $e) {
            $logs[] = 'Exception: ' . $e->getMessage();
            \Log::error('VFD exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return ['success' => false, 'logs' => $logs];
        }
    }

    private function configureWindowsPort(&$logs = [])
    {
        $parityCode = ['none' => 'n', 'odd' => 'o', 'even' => 'e'][$this->parity] ?? 'n';
        $command = "mode {$this->port}: BAUD={$this->baudRate} PARITY={$parityCode} DATA={$this->dataBits} STOP={$this->stopBits}";
        $logs[] = "Running: $command";
        exec($command, $output, $exitCode);
        $logs[] = "Exit code: $exitCode, Output: " . implode(' ', $output);
        \Log::info('Windows VFD port config', ['command' => $command, 'output' => $output, 'code' => $exitCode]);
    }

    private function configureLinuxPort(&$logs = [])
    {
        $parityArg = [
            'none' => '-parenb',
            'odd' => 'parenb parodd',
            'even' => 'parenb -parodd'
        ][$this->parity] ?? '-parenb';

        $command = "stty -F {$this->port} {$this->baudRate} cs{$this->dataBits} " .
            ($this->stopBits == 2 ? 'cstopb' : '-cstopb') . " $parityArg -echo -echoe -echok -echoctl -echoke -icrnl -onlcr -opost -isig -icanon -iexten";

        $logs[] = "Running: $command";
        exec($command, $output, $exitCode);
        $logs[] = "Exit code: $exitCode, Output: " . implode(' ', $output);
        \Log::info('Linux VFD port config', ['command' => $command, 'output' => $output, 'code' => $exitCode]);
    }
}
