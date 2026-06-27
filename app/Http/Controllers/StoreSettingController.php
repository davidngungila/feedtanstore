<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class StoreSettingController extends Controller
{
    public function index()
    {
        // Get or create the first (and only) store settings
        $settings = StoreSetting::firstOrCreate();
        return view('store.profile', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = StoreSetting::firstOrCreate();
        \Log::info('StoreSettingController update - Request received:', $request->all());
        $request->validate([
            'store_name' => 'sometimes|required|string|max:255',
            'store_email' => 'nullable|email|max:255',
            'store_phone' => 'nullable|string|max:255',
            'store_address' => 'nullable|string',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'currency' => 'sometimes|required|string|max:10',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_name' => 'nullable|string|max:255',
            'tax_enabled' => 'boolean',
            'receipt_footer' => 'nullable|string',
            'receipt_header' => 'nullable|string',
            'receipt_show_logo' => 'boolean',
            'receipt_show_tax' => 'boolean',
            'enable_loyalty' => 'boolean',
            'kiosk_mode_enabled' => 'boolean',
            'kiosk_force_fullscreen' => 'boolean',
            'kiosk_block_right_click' => 'boolean',
            'kiosk_prevent_tab_switch' => 'boolean',
            'kiosk_lock_keyboard_shortcuts' => 'boolean',
            'kiosk_auto_focus_cashier' => 'boolean',
            'barcode_type' => 'nullable|string|max:255',
            'barcode_width' => 'nullable|integer|min:100|max:1000',
            'barcode_height' => 'nullable|integer|min:50|max:500',
            'barcode_show_text' => 'boolean',
            'openrouteservice_api_key' => 'nullable|string',
            'store_latitude' => 'nullable|numeric',
            'store_longitude' => 'nullable|numeric',
            'share_price' => 'nullable|numeric|min:0',
            // Communication fields
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
            'email_from_address' => 'nullable|email|max:255',
            'email_from_name' => 'nullable|string|max:255',
            'sms_provider' => 'nullable|string|max:255',
            'sms_api_key' => 'nullable|string|max:255',
            'sms_api_secret' => 'nullable|string|max:255',
            'sms_from_number' => 'nullable|string|max:255',
            'messaging_sender_id' => 'nullable|string|max:255',
            // VFD fields
            'vfd_enabled' => 'boolean',
            'vfd_port' => 'nullable|string|max:255',
            'vfd_baud' => 'nullable|integer|min:300|max:115200',
            'vfd_data_bits' => 'nullable|integer|in:5,6,7,8',
            'vfd_stop_bits' => 'nullable|integer|in:1,2',
            'vfd_parity' => 'nullable|string|in:none,odd,even',
        ]);

        $data = $request->all();
        
        // Handle checkbox fields - default to false if not present in request
        $checkboxFields = [
            'tax_enabled',
            'receipt_show_logo',
            'receipt_show_tax',
            'enable_loyalty',
            'kiosk_mode_enabled',
            'kiosk_force_fullscreen',
            'kiosk_block_right_click',
            'kiosk_prevent_tab_switch',
            'kiosk_lock_keyboard_shortcuts',
            'kiosk_auto_focus_cashier',
            'barcode_show_text',
            'vfd_enabled'
        ];
        
        foreach ($checkboxFields as $field) {
            $data[$field] = $request->has($field) ? true : false;
        }
        
        \Log::info('StoreSettingController update - Data after checkbox processing:', $data);

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            // Delete old logo if exists
            if ($settings->store_logo && Storage::exists('public/' . $settings->store_logo)) {
                Storage::delete('public/' . $settings->store_logo);
            }
            $path = $request->file('store_logo')->store('store-logos', 'public');
            $data['store_logo'] = $path;
        }

        $settings->update($data);
        
        $settings2 = StoreSetting::first();
        \Log::info('StoreSettingController update - Settings after saving:', $settings2->toArray());

        return back()->with('success', 'Settings updated successfully!');
    }

    public function settingsPage()
    {
        $settings = StoreSetting::firstOrCreate();
        return view('store.settings', compact('settings'));
    }

    public function general()
    {
        $settings = StoreSetting::firstOrCreate();
        return view('system.general', compact('settings'));
    }

    public function tax()
    {
        $settings = StoreSetting::firstOrCreate();
        return view('system.tax', compact('settings'));
    }

    public function receipt()
    {
        $settings = StoreSetting::firstOrCreate();
        return view('system.receipt', compact('settings'));
    }

    public function barcode()
    {
        $settings = StoreSetting::firstOrCreate();
        return view('system.barcode', compact('settings'));
    }

    public function backup()
    {
        return view('system.backup');
    }

    public function database()
    {
        return view('system.database');
    }

    public function communication()
    {
        $settings = StoreSetting::firstOrCreate();
        $communicationProfiles = \App\Models\CommunicationProfile::latest()->get();
        return view('system.communication', compact('settings', 'communicationProfiles'));
    }

    public function logs()
    {
        $logFiles = [];
        $logPath = storage_path('logs');
        
        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                $logFiles[] = [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                    'path' => $file->getPathname()
                ];
            }
        }
        
        return view('system.logs', compact('logFiles'));
    }

    public function createBackup()
    {
        try {
            Artisan::call('backup:run --only-db');
            return back()->with('success', 'Backup created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating backup: ' . $e->getMessage());
        }
    }

    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (File::exists($path)) {
            return response()->download($path);
        }
        return back()->with('error', 'Backup file not found!');
    }

    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs');
            $files = File::files($logPath);
            foreach ($files as $file) {
                File::put($file->getPathname(), '');
            }
            return back()->with('success', 'Logs cleared successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error clearing logs: ' . $e->getMessage());
        }
    }

    public function vfd()
    {
        $settings = StoreSetting::firstOrCreate();
        return view('system.vfd', compact('settings'));
    }

    public function testVfd(Request $request)
    {
        try {
            $logs = [];
            $logs[] = "========== VFD Test Started ==========";
            
            // Get values from POST request (form values, not DB!)
            $vfdEnabled = $request->input('vfd_enabled', false);
            $vfdPort = $request->input('vfd_port', 'COM3');
            $vfdBaud = $request->input('vfd_baud', 9600);
            $vfdDataBits = $request->input('vfd_data_bits', 8);
            $vfdStopBits = $request->input('vfd_stop_bits', 1);
            $vfdParity = $request->input('vfd_parity', 'none');
            
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            $logs[] = "1. Operating System: " . ($isWindows ? "Windows" : "Linux");
            $logs[] = "2. VFD Enabled: " . ($vfdEnabled ? "Yes" : "No");
            $logs[] = "3. Port: " . $vfdPort;
            $logs[] = "4. Baud Rate: " . $vfdBaud;
            $logs[] = "5. Data Bits: " . $vfdDataBits;
            $logs[] = "6. Stop Bits: " . $vfdStopBits;
            $logs[] = "7. Parity: " . $vfdParity;
            
            if (!$vfdEnabled) {
                $logs[] = "ERROR: VFD is not enabled - enable it in the form!";
                return response()->json([
                    'success' => false,
                    'message' => 'VFD not enabled',
                    'logs' => $logs
                ]);
            }
            
            // Configure port first
            $logs[] = "";
            $logs[] = "========== Step 1: Configuring port... ==========";
            try {
                if ($isWindows) {
                    $parityCode = ['none' => 'n', 'odd' => 'o', 'even' => 'e'][$vfdParity] ?? 'n';
                    $modeCommand = "mode " . $vfdPort . ": BAUD=" . $vfdBaud . " PARITY=" . $parityCode . " DATA=" . $vfdDataBits . " STOP=" . $vfdStopBits;
                    $logs[] = "Running command: " . $modeCommand;
                    exec($modeCommand, $cmdOutput, $exitCode);
                    $logs[] = "Command exit code: " . $exitCode;
                    if ($exitCode === 0) {
                        $logs[] = "SUCCESS: Port configuration command completed";
                    } else {
                        $logs[] = "WARNING: Port configuration might have failed (exit code: $exitCode)";
                    }
                } else {
                    $parityArg = [
                        'none' => '-parenb',
                        'odd' => 'parenb parodd',
                        'even' => 'parenb -parodd'
                    ][$vfdParity] ?? '-parenb';
                    $sttyCmd = "stty -F " . $vfdPort . " " . $vfdBaud . " cs" . $vfdDataBits . " " . ($vfdStopBits == 2 ? 'cstopb' : '-cstopb') . " " . $parityArg . " -echo -echoe -echok -echoctl -echoke -icrnl -onlcr -opost -isig -icanon -iexten";
                    $logs[] = "Running command: " . $sttyCmd;
                    exec($sttyCmd, $cmdOutput, $exitCode);
                    $logs[] = "Command exit code: " . $exitCode;
                    if ($exitCode === 0) {
                        $logs[] = "SUCCESS: Port configuration completed";
                    } else {
                        $logs[] = "WARNING: Port configuration might have failed (exit code: $exitCode)";
                    }
                }
            } catch (\Exception $configErr) {
                $logs[] = "WARNING: Port config exception: " . $configErr->getMessage();
            }
            
            // Now open the port
            $logs[] = "";
            $logs[] = "========== Step 2: Opening port... ==========";
            $handle = @fopen($vfdPort, 'w');
            if (!$handle) {
                $error = error_get_last();
                $logs[] = "FAILED: Could not open port! This means either: 1) the port doesn't exist, 2) is already in use, 3) you don't have permission!";
                $logs[] = "Error Details: " . ($error['message'] ?? 'Unknown error');
                return response()->json([
                    'success' => false,
                    'message' => 'Could not open port',
                    'logs' => $logs
                ]);
            }
            $logs[] = "SUCCESS: Port opened successfully!";
            
            // Send test data
            $logs[] = "";
            $logs[] = "========== Step 3: Sending test messages ==========";
            $testMsgLine1 = "WELCOME";
            $testMsgLine2 = "FEEDTAN";
            $logs[] = "Test data: \"$testMsgLine1\", \"$testMsgLine2\"";
            
            // Try different initialization sequences (Multi-protocol test)
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
                $fullMsg = $generator($testMsgLine1, $testMsgLine2);
                $bytesWritten = fwrite($handle, $fullMsg);
                $logs[] = "Protocol $name: wrote $bytesWritten bytes";
                fflush($handle);
                usleep(250000); // 250ms between attempts
            }
            
            fclose($handle);
            $logs[] = "";
            $logs[] = "========== Step 4: Port closed ==========";
            $logs[] = "SUCCESS: All test messages sent! Check your VFD display now!";
            $logs[] = "========== Test Complete ==========";
            return response()->json([
                'success' => true,
                'message' => 'Test completed',
                'logs' => $logs
            ]);
            
        } catch (\Exception $e) {
            $logs[] = "EXCEPTION: " . $e->getMessage();
            $logs[] = $e->getTraceAsString();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'logs' => $logs
            ]);
        }
    }
}
