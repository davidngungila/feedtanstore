<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'receipt_footer' => 'nullable|string',
            'enable_loyalty' => 'boolean',
            'kiosk_mode_enabled' => 'boolean',
            'kiosk_force_fullscreen' => 'boolean',
            'kiosk_block_right_click' => 'boolean',
            'kiosk_prevent_tab_switch' => 'boolean',
            'kiosk_lock_keyboard_shortcuts' => 'boolean',
            'kiosk_auto_focus_cashier' => 'boolean'
        ]);

        $data = $request->all();
        
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

        return back()->with('success', 'Store settings updated successfully!');
    }

    public function settingsPage()
    {
        $settings = StoreSetting::firstOrCreate();
        return view('store.settings', compact('settings'));
    }
}
