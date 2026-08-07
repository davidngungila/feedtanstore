<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, update any existing roles that might not be in the new enum
        DB::statement("UPDATE users SET role = 'admin' WHERE role NOT IN ('admin', 'cashier', 'manager', 'storekeeper', 'marketing_officer')");
        
        // Then modify the column using raw SQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'cashier', 'manager', 'storekeeper', 'marketing_officer') NOT NULL DEFAULT 'admin'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) NOT NULL DEFAULT 'admin'");
    }
};
