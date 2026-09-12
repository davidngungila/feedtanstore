<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','cashier','storekeeper','marketing_officer','field_sales','stock_auditor','external_auditor','online_sales','inventory_manager','rider','store_supervisor') NOT NULL DEFAULT 'admin'");
        // Ensure role_permissions has entries for store_supervisor
        $roles = ['store_supervisor'];
        $modules = ['sales', 'inventory', 'purchasing', 'hr', 'finance', 'reports', 'marketing', 'system', 'stock_verification', 'customer_demand', 'competitor_intel', 'field_sales', 'online_orders', 'audit', 'cashier_performance'];
        foreach ($roles as $role) {
            foreach ($modules as $module) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role' => $role, 'module' => $module],
                    [
                        'can_create' => in_array($module, ['sales','inventory','stock_verification','customer_demand','competitor_intel','online_orders','audit','cashier_performance','reports']) ? 1 : 0,
                        'can_read' => 1,
                        'can_update' => in_array($module, ['sales','inventory','stock_verification','customer_demand','competitor_intel','online_orders','audit','cashier_performance','reports','purchasing']) ? 1 : 0,
                        'can_delete' => in_array($module, ['audit']) ? 0 : 0,
                    ]
                );
            }
        }
        // Store supervisor can manage most issues: give full on transaction issues via audit module and sales returns
        DB::table('role_permissions')->updateOrInsert(
            ['role' => 'store_supervisor', 'module' => 'audit'],
            ['can_create'=>1,'can_read'=>1,'can_update'=>1,'can_delete'=>0]
        );
    }
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','cashier','storekeeper','marketing_officer','field_sales','stock_auditor','external_auditor','online_sales','inventory_manager','rider') NOT NULL DEFAULT 'admin'");
        DB::table('role_permissions')->where('role','store_supervisor')->delete();
    }
};
