<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('kiosk_mode_enabled')->default(false);
            $table->boolean('kiosk_force_fullscreen')->default(false);
            $table->boolean('kiosk_block_right_click')->default(false);
            $table->boolean('kiosk_prevent_tab_switch')->default(false);
            $table->boolean('kiosk_lock_keyboard_shortcuts')->default(false);
            $table->boolean('kiosk_auto_focus_cashier')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'kiosk_mode_enabled',
                'kiosk_force_fullscreen',
                'kiosk_block_right_click',
                'kiosk_prevent_tab_switch',
                'kiosk_lock_keyboard_shortcuts',
                'kiosk_auto_focus_cashier'
            ]);
        });
    }
};
