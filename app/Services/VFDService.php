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
    private $protocol;

    // Define all available protocols
    private $availableProtocols = [
        'esc_at' => 'ESC @ Init',
        'form_feed' => 'Form Feed',
        'esc_at_home' => 'ESC @ + Home',
        'clear_display' => 'Clear Display (ESC [2J)',
        'pos' => 'POS Protocol',
        'epson' => 'Epson-like',
        'simple' => 'Simple No Init',
    ];

    public function __construct()
    {
        $settings = StoreSetting::firstOrCreate();

        $this->enabled = $settings->vfd_enabled ?? false;
        $this->port = $settings->vfd_port ?? env('VFD_PORT', 'COM3');
        $this->baudRate = $settings->vfd_baud ?? env('VFD_BAUD', 9600);
        $this->dataBits = $settings->vfd_data_bits ?? env('VFD_DATA_BITS', 8);
        $this->stopBits = $settings->vfd_stop_bits ?? env('VFD_STOP_BITS', 1);
        $this->parity = $settings->vfd_parity ?? env('VFD_PARITY', 'none');
        $this->protocol = $settings->vfd_protocol ?? 'esc_at';
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public function getAvailableProtocols()
    {
        return $this->availableProtocols;
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

    public static function openCashDrawer()
    {
        try {
            $settings = StoreSetting::first();
            if (!$settings || !$settings->vfd_enabled) {
                return;
            }

            $port = $settings->vfd_port ?? env('VFD_PORT', 'COM3');
            $baudRate = $settings->vfd_baud ?? env('VFD_BAUD', 9600);
            $dataBits = $settings->vfd_data_bits ?? env('VFD_DATA_BITS', 8);
            $stopBits = $settings->vfd_stop_bits ?? env('VFD_STOP_BITS', 1);
            $parity = $settings->vfd_parity ?? env('VFD_PARITY', 'none');
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

            // Configure port
            if ($isWindows) {
                $parityCode = ['none' => 'n', 'odd' => 'o', 'even' => 'e'][$parity] ?? 'n';
                $command = "mode {$port}: BAUD={$baudRate} PARITY={$parityCode} DATA={$dataBits} STOP={$stopBits}";
                exec($command, $output, $exitCode);
            } else {
                $parityArg = [
                    'none' => '-parenb',
                    'odd' => 'parenb parodd',
                    'even' => 'parenb -parodd'
                ][$parity] ?? '-parenb';
                $command = "stty -F {$port} {$baudRate} cs{$dataBits} " .
                    ($stopBits == 2 ? 'cstopb' : '-cstopb') . " $parityArg -echo -echoe -echok -echoctl -echoke -icrnl -onlcr -opost -isig -icanon -iexten";
                exec($command, $output, $exitCode);
            }

            // Open port and send cash drawer pulse command (ESC p m t)
            // ESC = 0x1B, p = 0x70, m = drawer pin (0=pin2, 1=pin5), t = pulse duration (x 2ms)
            $handle = @fopen($port, 'w');
            if ($handle) {
                // ESC p m t - Pulse drawer pin 2 for 100ms (t=50)
                $command = "\x1B\x70\x00\x32";
                fwrite($handle, $command);
                fflush($handle);
                fclose($handle);
                \Log::info('Cash drawer pulse sent successfully');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to open cash drawer: ' . $e->getMessage());
        }
    }

    private function getProtocolGenerator($protocolName)
    {
        $protocols = [
            'esc_at' => function ($l1, $l2) { return "\x1B@" . $l1 . "\r\n" . $l2 . "\r\n"; },
            'form_feed' => function ($l1, $l2) { return "\x0C" . $l1 . "\r\n" . $l2 . "\r\n"; },
            'esc_at_home' => function ($l1, $l2) { return "\x1B@" . "\x1B[H" . $l1 . "\r\n" . $l2 . "\r\n"; },
            'clear_display' => function ($l1, $l2) { return "\x1B[2J" . $l1 . "\r\n" . $l2 . "\r\n"; },
            'pos' => function ($l1, $l2) { return "\x1B@" . "\x1B" . "|" . "lA" . $l1 . "\r\n" . $l2 . "\r\n" . "\x0C"; },
            'epson' => function ($l1, $l2) { return "\x1B@" . "\x1B" . "c" . "\x03" . $l1 . "\r\n" . $l2 . "\r\n" . "\x0C"; },
            'simple' => function ($l1, $l2) { return $l1 . "\r\n" . $l2 . "\r\n"; },
        ];

        return $protocols[$protocolName] ?? $protocols['esc_at'];
    }

    private function sendVFDMessage($line1, $line2 = '')
    {
        $logs = [];
        $logs[] = "VFD Config: Enabled=". ($this->enabled ? 'Yes' : 'No') . ", Port={$this->port}, Baud={$this->baudRate}, Protocol={$this->protocol}";

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

            $generator = $this->getProtocolGenerator($this->protocol);
            $message = $generator($line1, $line2);
            $bytesWritten = fwrite($handle, $message);
            fflush($handle);
            $logs[] = "Protocol " . $this->availableProtocols[$this->protocol] . ": wrote $bytesWritten bytes";

            fclose($handle);
            \Log::info('VFD sent successfully', $logs);

            return ['success' => true, 'logs' => $logs];
        } catch (\Exception $e) {
            $logs[] = 'Exception: ' . $e->getMessage();
            \Log::error('VFD exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return ['success' => false, 'logs' => $logs];
        }
    }

    // Also add a method that can test ALL protocols (for the test button!)
    public function testAllProtocols($line1, $line2)
    {
        $logs = [];
        $logs[] = "=== Testing All VFD Protocols ===";
        $logs[] = "VFD Config: Port={$this->port}, Baud={$this->baudRate}";

        try {
            if ($this->isWindows) {
                $this->configureWindowsPort($logs);
            } else {
                $this->configureLinuxPort($logs);
            }

            $handle = @fopen($this->port, 'w');
            if (!$handle) {
                $error = error_get_last();
                $logs[] = "ERROR: Cannot open port {$this->port}: " . ($error['message'] ?? 'Unknown');
                return ['success' => false, 'logs' => $logs];
            }

            $protocols = [
                'esc_at' => 'ESC @ Init',
                'form_feed' => 'Form Feed',
                'esc_at_home' => 'ESC @ + Home',
                'clear_display' => 'Clear Display (ESC [2J)',
                'pos' => 'POS Protocol',
                'epson' => 'Epson-like',
                'simple' => 'Simple No Init',
            ];

            foreach ($protocols as $key => $name) {
                $generator = $this->getProtocolGenerator($key);
                $message = $generator($line1, $line2);
                $bytes = fwrite($handle, $message);
                fflush($handle);
                $logs[] = "Protocol $name: wrote $bytes bytes";
                usleep(300000);
            }

            fclose($handle);
            return ['success' => true, 'logs' => $logs];
        } catch (\Exception $e) {
            $logs[] = 'Exception: ' . $e->getMessage();
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
