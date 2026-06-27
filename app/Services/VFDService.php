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

    /**
     * Display welcome message on VFD
     */
    public function displayWelcome()
    {
        $message = "\x1B\x40"; // Initialize display
        $message .= "WELCOME\n";
        $message .= "\n";
        $message .= "FEEDTAN STORE";
        
        $this->sendToVFD($message);
    }

    /**
     * Display product on VFD
     */
    public function displayProduct($productName, $quantity, $price, $total)
    {
        $message = "\x1B\x40"; // Initialize display
        $message .= $productName . "\n";
        $message .= "\n";
        $message .= "Qty: " . $quantity . "\n";
        $message .= "\n";
        $message .= "TZS " . number_format($total, 0);
        
        $this->sendToVFD($message);
    }

    /**
     * Display payment info on VFD
     */
    public function displayPayment($total, $paid, $change, $paymentMethod)
    {
        $message = "\x1B\x40"; // Initialize display
        $message .= "TOTAL\n";
        $message .= "TZS " . number_format($total, 0) . "\n";
        $message .= "\n";
        $message .= strtoupper($paymentMethod) . "\n";
        $message .= number_format($paid, 0) . "\n";
        $message .= "\n";
        $message .= "CHANGE\n";
        $message .= number_format($change, 0);
        
        $this->sendToVFD($message);
    }

    /**
     * Display thank you message on VFD
     */
    public function displayThankYou()
    {
        $message = "\x1B\x40"; // Initialize display
        $message .= "THANK YOU\n";
        $message .= "\n";
        $message .= "COME AGAIN";
        
        $this->sendToVFD($message);
    }

    /**
     * Send message to VFD
     */
    private function sendToVFD($message)
    {
        // Log the message for debugging purposes
        \Log::info('VFD Output:', ['message' => $message]);

        // Check if VFD is enabled
        if (!$this->enabled) {
            \Log::info('VFD is disabled, skipping message');
            return;
        }

        try {
            if ($this->isWindows) {
                // Windows - Use mode command to configure and file_put_contents to write
                $this->configureWindowsPort();
                $handle = fopen($this->port, 'w');
                if ($handle) {
                    fwrite($handle, $message);
                    fclose($handle);
                } else {
                    \Log::error('Failed to open VFD port: ' . $this->port);
                }
            } else {
                // Linux - Use stty to configure and file_put_contents to write
                $this->configureLinuxPort();
                $handle = fopen($this->port, 'w');
                if ($handle) {
                    fwrite($handle, $message);
                    fclose($handle);
                } else {
                    \Log::error('Failed to open VFD port: ' . $this->port);
                }
            }
        } catch (\Exception $e) {
            \Log::error('VFD Communication Error: ' . $e->getMessage());
        }
    }

    /**
     * Configure serial port on Windows
     */
    private function configureWindowsPort()
    {
        $parityCode = ['none' => 'n', 'odd' => 'o', 'even' => 'e'][$this->parity] ?? 'n';
        $modeCommand = "mode {$this->port} BAUD={$this->baudRate} PARITY={$parityCode} DATA={$this->dataBits} STOP={$this->stopBits}";
        exec($modeCommand, $output, $resultCode);
        \Log::info('Windows VFD Port Config:', ['command' => $modeCommand, 'output' => $output, 'code' => $resultCode]);
    }

    /**
     * Configure serial port on Linux
     */
    private function configureLinuxPort()
    {
        $parityArg = [
            'none' => '-parenb',
            'odd' => 'parenb parodd',
            'even' => 'parenb -parodd'
        ][$this->parity] ?? '-parenb';
        
        $sttyCommand = "stty -F {$this->port} {$this->baudRate} cs{$this->dataBits} {$this->stopBits} {$parityArg} -cstopb -echo -echoe -echok -echoctl -echoke";
        exec($sttyCommand, $output, $resultCode);
        \Log::info('Linux VFD Port Config:', ['command' => $sttyCommand, 'output' => $output, 'code' => $resultCode]);
    }
}
