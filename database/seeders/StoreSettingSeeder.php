<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoreSetting::updateOrCreate(
            ['id' => 1],
            [
                'store_name' => 'FeedTan Store',
                'store_email' => 'info@feedtan.co.tz',
                'store_phone' => '+255 123 456 789',
                'store_address' => 'Moshi, Tanzania',
                'currency' => 'TZS',
                'tax_rate' => 18,
                'tax_name' => 'VAT',
                'tax_enabled' => true,
                'receipt_footer' => 'Thank you for shopping with FeedTan Store!',
                'receipt_header' => 'FeedTan Store - Quality Products',
                'receipt_show_logo' => true,
                'receipt_show_tax' => true,
                'enable_loyalty' => false,
                'kiosk_mode_enabled' => false,
                'kiosk_force_fullscreen' => false,
                'kiosk_block_right_click' => false,
                'kiosk_prevent_tab_switch' => false,
                'kiosk_lock_keyboard_shortcuts' => false,
                'kiosk_auto_focus_cashier' => false,
                'barcode_type' => 'C128',
                'barcode_width' => 300,
                'barcode_height' => 100,
                'barcode_show_text' => true,
                'store_latitude' => -3.3430,
                'store_longitude' => 37.3507,
                'store_url' => 'https://feedtanstore.com',
                'share_price' => 1000,
                // Delivery Fee Settings
                'delivery_base_fee' => 2000,
                'delivery_per_km_rate' => 400,
                'delivery_free_threshold' => 50000,
                'delivery_use_zone_pricing' => false,
                'delivery_zone_config' => null,
                // Communication Settings
                'smtp_host' => null,
                'smtp_port' => 587,
                'smtp_username' => null,
                'smtp_password' => null,
                'smtp_encryption' => 'tls',
                'email_from_address' => 'noreply@feedtan.co.tz',
                'email_from_name' => 'FeedTan Store',
                'sms_provider' => null,
                'sms_api_key' => null,
                'sms_api_secret' => null,
                'sms_from_number' => null,
                'messaging_sender_id' => 'FEEDTAN',
                // VFD Settings
                'vfd_enabled' => false,
                'vfd_port' => 'COM3',
                'vfd_baud' => 9600,
                'vfd_data_bits' => 8,
                'vfd_stop_bits' => 1,
                'vfd_parity' => 'none',
                'vfd_protocol' => 'esc_at',
            ]
        );
    }
}
