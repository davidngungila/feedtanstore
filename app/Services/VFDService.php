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
        // Try common VFD protocols
        $this->sendVFDMessage("WELCOME\r\nFEEDTAN STORE");
    }

    /**
     * Display product on VFD
     */
    public function displayProduct($productName, $quantity, $price, $total)
    {
        $message = substr($productName, 0, 20) . "\r\n";
        $message .= "Qty: " . $quantity . "  TZS " . number_format($total, 0);
        $this->sendVFDMessage($message);
    }

    /**
     * Display payment info on VFD
     */
    public function displayPayment($total, $paid, $change, $paymentMethod)
    {
        $message = "TOTAL: TZS " . number_format($total, 0) . "\r\n";
        $message .= strtoupper($paymentMethod) . ": " . number_format($paid, 0) . "\r\n";
        $message .= "CHANGE: TZS " . number_format($change, 0);
        $this->sendVFDMessage($message);
    }

    /**
     * Display thank you message on VFD
     */
    public function displayThankYou()
    {
        $this->sendVFDMessage("THANK YOU\r\nCOME AGAIN");
    }

    /**
     * Send message to VFD with better protocol handling
     */
    private function sendVFDMessage($message)
    {
        $logs = [];
        $logs[] = "VFD Configuration:";
        $logs[] = "  Enabled: " . ($this->enabled ? "Yes" : "No");
        $logs[] = "  Port: " . $this->port;
        $logs[] = "  Baud Rate: " . $this->baudRate;
        $logs[] = "  OS: " . ($this->isWindows ? "Windows" : "Linux");
        $logs[] = "Message: " . $message;

        // Log the message for debugging
        \Log::info('VFD Attempt', ['message' => $message, 'config' => [
            'enabled' => $this->enabled,
            'port' => $this->port,
            'baud' => $this->baudRate,
            'os' => $this->isWindows ? 'Windows' : 'Linux'
        ]]);

        // Check if VFD is enabled
        if (!$this->enabled) {
            $logs[] = "VFD is disabled, skipping message";
            \Log::info('VFD Disabled');
            return ['success' => false, 'logs' => $logs, 'error' => 'VFD is disabled'];
        }

        try {
            $success = false;
            
            if ($this->isWindows) {
                $logs[] = "Configuring Windows port...";
                $this->configureWindowsPort($logs);
                
                $logs[] = "Opening port: " . $this->port;
                $handle = @fopen($this->port, 'w');
                
                if ($handle) {
                    $logs[] = "Port opened successfully";
                    
                    // Try multiple VFD initialization sequences
                    $initCodes = [
                        "\x1B\x40", // ESC @ (initialize)
                        "\x0C",     // Form feed (clear screen)
                        ""          // No initialization
                    ];
                    
                    foreach ($initCodes as $i => $initCode) {
                        $fullMessage = $initCode . $message . "\r\n";
                        $bytesWritten = fwrite($handle, $fullMessage);
                        $logs[] = "Attempt " . ($i + 1) . ": Wrote " . $bytesWritten . " bytes";
                        fflush($handle);
                        usleep(100000); // 100ms delay
                    }
                    
                    fclose($handle);
                    $success = true;
                } else {
                    $error = error_get_last();
                    $logs[] = "Failed to open VFD port: " . ($error['message'] ?? 'Unknown error');
                    \Log::error('VFD Port Open Failed', ['port' => $this->port, 'error' => $error]);
                }
            } else {
                $logs[] = "Configuring Linux port...";
                $this->configureLinuxPort($logs);
                
                $logs[] = "Opening port: " . $this->port;
                $handle = @fopen($this->port, 'w');
                
                if ($handle) {
                    $logs[] = "Port opened successfully";
                    
                    // Try multiple VFD initialization sequences
                    $initCodes = [
                        "\x1B\x40", // ESC @ (initialize)
                        "\x0C",     // Form feed (clear screen)
                        ""          // No initialization
                    ];
                    
                    foreach ($initCodes as $i => $initCode) {
                        $fullMessage = $initCode . $message . "\r\n";
                        $bytesWritten = fwrite($handle, $fullMessage);
                        $logs[] = "Attempt " . ($i + 1) . ": Wrote " . $bytesWritten . " bytes";
                        fflush($handle);
                        usleep(100000); // 100ms delay
                    }
                    
                    fclose($handle);
                    $success = true;
                } else {
                    $error = error_get_last();
                    $logs[] = "Failed to open VFD port: " . ($error['message'] ?? 'Unknown error');
                    \Log::error('VFD Port Open Failed', ['port' => $this->port, 'error' => $error]);
                }
            }

            \Log::info('VFD Result', ['success' => $success, 'logs' => $logs]);
            return ['success' => $success, 'logs' => $logs];
            
        } catch (\Exception $e) {
            $logs[] = "Exception: " . $e->getMessage();
            \Log::error('VFD Exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return ['success' => false, 'logs' => $logs, 'error' => $e->getMessage()];
        }
    }

    /**
     * Configure serial port on Windows
     */
    private function configureWindowsPort(&$logs = [])
    {
        $parityCode = ['none' => 'n', 'odd' => 'o', 'even' => 'e'][$this->parity] ?? 'n';
        $modeCommand = "mode {$this->port}: BAUD={$this->baudRate} PARITY={$parityCode} DATA={$this->dataBits} STOP={$this->stopBits}";
        $logs[] = "Running Windows config: " . $modeCommand;
        
        exec($modeCommand, $output, $resultCode);
        $logs[] = "Mode exit code: " . $resultCode;
        $logs[] = "Mode output: " . implode("\n", $output);
        
        \Log::info('Windows VFD Port Config', ['command' => $modeCommand, 'output' => $output, 'code' => $resultCode]);
    }

    /**
     * Configure serial port on Linux
     */
    private function configureLinuxPort(&$logs = [])
    {
        $parityArg = [
            'none' => '-parenb',
            'odd' => 'parenb parodd',
            'even' => 'parenb -parodd'
        ][$this->parity] ?? '-parenb';
        
        $sttyCommand = "stty -F {$this->port} {$this->baudRate} cs{$this->dataBits} " . 
                       ($this->stopBits == 2 ? 'cstopb' : '-cstopb') . " " . 
                       $parityArg . " -echo -echoe -echok -echoctl -echoke -icrnl -onlcr -opost -isig -icanon -iexten";
        
        $logs[] = "Running Linux config: " . $sttyCommand;
        
        exec($sttyCommand, $output, $resultCode);
        $logs[] = "Stty exit code: " . $resultCode;
        $logs[] = "Stty output: " . implode("\n", $output);
        
        \Log::info('Linux VFD Port Config', ['command' => $sttyCommand, 'output' => $output, 'code' => $resultCode]);
    }
}
