<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class TraVfdSeeder extends Seeder
{
    /**
     * Seed TRA VFD API settings for the Tanzania Revenue Authority
     * Electronic Fiscal Device integration (PERG_TRA_VFD_API_v1.0.1).
     *
     * Usage: php artisan db:seed --class=TraVfdSeeder
     */
    public function run(): void
    {
        StoreSetting::where('id', 1)->update([
            // TRA VFD API Settings (Test Environment)
            'tra_api_endpoint' => 'http://162.55.181.173:8080/TRA_VFD/Operations',
            'tra_api_username' => '0756880647',
            'tra_api_password' => 'israel_',
            'tra_tin_number' => '110781512',
            'tra_vfd_serial' => '03TZ843010734',
            'tra_licence' => '3V7+fwCPqFZf54roeksJ//ZQMLkBNs07d1z/Cm03lI8lA2FpnOGCdKwZCr7s0oSb',
        ]);
    }
}
