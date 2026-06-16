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
        'receipt_footer',
        'enable_loyalty',
        'kiosk_mode_enabled',
        'kiosk_force_fullscreen',
        'kiosk_block_right_click',
        'kiosk_prevent_tab_switch',
        'kiosk_lock_keyboard_shortcuts',
        'kiosk_auto_focus_cashier'
    ];
}
