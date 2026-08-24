<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('module');
            $table->boolean('can_create')->default(true);
            $table->boolean('can_read')->default(true);
            $table->boolean('can_update')->default(true);
            $table->boolean('can_delete')->default(true);
            $table->timestamps();

            $table->unique(['role', 'module']);
        });

        $roles = ['admin', 'manager', 'cashier', 'storekeeper', 'marketing_officer'];
        $modules = ['sales', 'inventory', 'purchasing', 'hr', 'finance', 'reports', 'marketing', 'system'];

        $rows = [];
        foreach ($roles as $role) {
            foreach ($modules as $module) {
                $rows[] = [
                    'role' => $role,
                    'module' => $module,
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('role_permissions')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
