<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add accountant back for backward compatibility with existing UI
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','cashier','storekeeper','marketing_officer','field_sales','stock_auditor','external_auditor','online_sales','inventory_manager','rider','store_supervisor','accountant') NOT NULL DEFAULT 'admin'");
        // Ensure role_permissions has accountant entries
        $modules = ['sales', 'inventory', 'purchasing', 'hr', 'finance', 'reports', 'marketing', 'system', 'stock_verification', 'customer_demand', 'competitor_intel', 'field_sales', 'online_orders', 'audit', 'cashier_performance'];
        foreach ($modules as $module) {
            DB::table('role_permissions')->updateOrInsert(
                ['role' => 'accountant', 'module' => $module],
                [
                    'can_create' => in_array($module, ['finance','reports']) ? 1 : 0,
                    'can_read' => in_array($module, ['finance','reports','sales','inventory','purchasing']) ? 1 : 0,
                    'can_update' => in_array($module, ['finance']) ? 1 : 0,
                    'can_delete' => 0,
                ]
            );
        }
    }
    public function down(): void
    {
        DB::table('role_permissions')->where('role','accountant')->delete();
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','cashier','storekeeper','marketing_officer','field_sales','stock_auditor','external_auditor','online_sales','inventory_manager','rider','store_supervisor') NOT NULL DEFAULT 'admin'");
    }
};
