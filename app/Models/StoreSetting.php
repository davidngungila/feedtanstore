<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name',
        'store_email',
        'store_phone',
        'store_address',
        'store_logo',
        'currency',
        'tax_rate',
        'tax_name',
        'tax_enabled',
        'receipt_footer',
        'receipt_header',
        'receipt_show_logo',
        'receipt_show_tax',
        'enable_loyalty',
        'kiosk_mode_enabled',
        'kiosk_force_fullscreen',
        'kiosk_block_right_click',
        'kiosk_prevent_tab_switch',
        'kiosk_lock_keyboard_shortcuts',
        'kiosk_auto_focus_cashier',
        'barcode_type',
        'barcode_width',
        'barcode_height',
        'barcode_show_text',
        'openrouteservice_api_key',
        'store_latitude',
        'store_longitude'
    ];
    
    protected $casts = [
        'tax_enabled' => 'boolean',
        'receipt_show_logo' => 'boolean',
        'receipt_show_tax' => 'boolean',
        'enable_loyalty' => 'boolean',
        'kiosk_mode_enabled' => 'boolean',
        'kiosk_force_fullscreen' => 'boolean',
        'kiosk_block_right_click' => 'boolean',
        'kiosk_prevent_tab_switch' => 'boolean',
        'kiosk_lock_keyboard_shortcuts' => 'boolean',
        'kiosk_auto_focus_cashier' => 'boolean',
        'barcode_show_text' => 'boolean'
    ];
}
