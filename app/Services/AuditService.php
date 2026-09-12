<?php

namespace App\Services;

use App\Models\ActionLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public static function log(string $action, string $module, ?string $recordType = null, $recordId = null, $oldValues = null, $newValues = null, ?string $details = null): void
    {
        try {
            ActionLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => $module,
                'record_type' => $recordType,
                'record_id' => $recordId,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'details' => $details ?? $action,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Audit log failed: '.$e->getMessage());
        }
    }
}
