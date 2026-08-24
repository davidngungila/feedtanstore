<?php

namespace App\Support;

use App\Models\RolePermission;

class Permissions
{
    public const ROLES = ['admin', 'manager', 'cashier', 'storekeeper', 'marketing_officer'];

    public const MODULES = ['sales', 'inventory', 'purchasing', 'hr', 'finance', 'reports', 'marketing', 'system'];

    public const ACTIONS = ['create', 'read', 'update', 'delete'];

    protected static ?array $matrix = null;

    protected static bool $tableAvailable = true;

    public static function matrix(): array
    {
        if (self::$matrix !== null) {
            return self::$matrix;
        }

        // Fail-open defaults: everything allowed until the table exists / row found.
        // Keeps the whole site rendering even before migrations have been run.
        $matrix = [];
        foreach (self::MODULES as $module) {
            foreach (self::ROLES as $role) {
                foreach (self::ACTIONS as $action) {
                    $matrix[$module][$role][$action] = true;
                }
            }
        }

        if (self::$tableAvailable) {
            try {
                foreach (RolePermission::all() as $perm) {
                    foreach (self::ACTIONS as $action) {
                        $matrix[$perm->module][$perm->role][$action] = $perm->{'can_' . $action};
                    }
                }
            } catch (\Throwable $e) {
                // Table missing (pre-migration) or DB issue — fall back to defaults.
                self::$tableAvailable = false;
            }
        }

        return self::$matrix = $matrix;
    }

    public static function allows(string $role, string $module, string $action): bool
    {
        if (!in_array($role, self::ROLES, true)) {
            return false;
        }

        return self::matrix()[$module][$role][$action] ?? true;
    }

    public static function sync(array $permissions): void
    {
        foreach (self::MODULES as $module) {
            foreach (self::ROLES as $role) {
                RolePermission::updateOrCreate(
                    ['role' => $role, 'module' => $module],
                    [
                        'can_create' => (bool) ($permissions[$module][$role]['create'] ?? false),
                        'can_read' => (bool) ($permissions[$module][$role]['read'] ?? false),
                        'can_update' => (bool) ($permissions[$module][$role]['update'] ?? false),
                        'can_delete' => (bool) ($permissions[$module][$role]['delete'] ?? false),
                    ]
                );
            }
        }

        self::$tableAvailable = true;
        self::$matrix = null;
    }
}
