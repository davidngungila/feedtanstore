<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','cashier','storekeeper','marketing_officer','field_sales','stock_auditor','external_auditor','online_sales','inventory_manager','rider') NOT NULL DEFAULT 'admin'");
    }
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','cashier','manager','storekeeper','marketing_officer') NOT NULL DEFAULT 'admin'");
    }
};
