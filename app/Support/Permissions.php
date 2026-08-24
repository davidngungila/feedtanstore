<?php

namespace App\Support;

use App\Models\RolePermission;

class Permissions
{
    public const ROLES = ['admin', 'manager', 'cashier', 'storekeeper', 'marketing_officer'];

    public const MODULES = ['sales', 'inventory', 'purchasing', 'hr', 'finance', 'reports', 'marketing', 'system'];

    public const ACTIONS = ['create', 'read', 'update', 'delete'];

    protected static ?array $matrix = null;

    public static function matrix(): array
    {
        if (self::$matrix !== null) {
            return self::$matrix;
        }

        $matrix = [];
        foreach (RolePermission::all() as $perm) {
            foreach (self::ACTIONS as $action) {
                $matrix[$perm->module][$perm->role][$action] = $perm->{'can_' . $action};
            }
        }

        // Default to allowed when a combination is missing so nothing gets locked out accidentally
        foreach (self::MODULES as $module) {
            foreach (self::ROLES as $role) {
                foreach (self::ACTIONS as $action) {
                    $matrix[$module][$role][$action] = $matrix[$module][$role][$action] ?? true;
                }
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

        self::$matrix = null;
    }
}
