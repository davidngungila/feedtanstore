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
        'store_longitude',
        'store_url',
        'share_price',
        // Delivery Fee Settings
        'delivery_base_fee',
        'delivery_per_km_rate',
        'delivery_free_threshold',
        'delivery_use_zone_pricing',
        'delivery_zone_config',
        // Communication Settings
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'email_from_address',
        'email_from_name',
        'sms_provider',
        'sms_api_key',
        'sms_api_secret',
        'sms_from_number',
        'messaging_sender_id',
        // VFD Settings
        'vfd_enabled',
        'vfd_port',
        'vfd_baud',
        'vfd_data_bits',
        'vfd_stop_bits',
        'vfd_parity',
        'vfd_protocol',
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
        'barcode_show_text' => 'boolean',
        'vfd_enabled' => 'boolean',
        'delivery_use_zone_pricing' => 'boolean',
        'delivery_zone_config' => 'array',
    ];

    /**
     * Calculate distance between two points using Haversine formula (in kilometers)
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Calculate delivery fee based on distance and order subtotal
     */
    public function calculateDeliveryFee(float $customerLat, float $customerLon, float $orderSubtotal): float
    {
        // If order meets free delivery threshold, return 0
        if ($orderSubtotal >= $this->delivery_free_threshold) {
            return 0;
        }

        $distance = $this->calculateDistance(
            $this->store_latitude ?? 0,
            $this->store_longitude ?? 0,
            $customerLat,
            $customerLon
        );

        if ($this->delivery_use_zone_pricing && $this->delivery_zone_config) {
            foreach ($this->delivery_zone_config as $zone) {
                if ($distance >= $zone['min_km'] && $distance <= $zone['max_km']) {
                    return (float) $zone['fee'];
                }
            }
            // If no zone matches, use last zone's fee
            $lastZone = end($this->delivery_zone_config);
            return $lastZone ? (float) $lastZone['fee'] : $this->delivery_base_fee;
        }

        // Distance-based pricing: Base + (Distance × Rate)
        $fee = $this->delivery_base_fee + ($distance * $this->delivery_per_km_rate);
        return max(0, round($fee, 2));
    }
}
}
