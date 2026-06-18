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
        
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_email' => 'nullable|email|max:255',
            'store_phone' => 'nullable|string|max:255',
            'store_address' => 'nullable|string',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'currency' => 'required|string|max:10',
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
            'store_longitude' => 'nullable|numeric'
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
            'barcode_show_text'
        ];
        
        foreach ($checkboxFields as $field) {
            $data[$field] = $request->has($field) ? true : false;
        }
        
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
}
