<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Models\LoginHistory;
use App\Models\UserDevice;
use App\Models\StoreSetting;
use App\Support\Permissions;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    protected function ensureRead(): void
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized. Only administrators and managers can view security pages.');
        }
    }

    protected function ensureAdmin(): void
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only administrators can perform this action.');
        }
    }

    // Access Control
    public function access()
    {
        $this->ensureAdmin();

        return view('security.access', [
            'roles' => Permissions::ROLES,
            'modules' => Permissions::MODULES,
            'actions' => Permissions::ACTIONS,
            'matrix' => Permissions::matrix(),
            'canManage' => Auth::user()->role === 'admin',
        ]);
    }

    // Audit Logs
    public function audit()
    {
        $this->ensureRead();

        $logs = ActionLog::with('user')->latest()->paginate(50);
        return view('security.audit', compact('logs'));
    }

    // Login History
    public function logins()
    {
        $this->ensureRead();

        $history = LoginHistory::with('user')->latest()->paginate(50);
        return view('security.logins', compact('history'));
    }

    // Device Management
    public function devices()
    {
        $this->ensureRead();

        $devices = UserDevice::with('user')->latest()->paginate(50);
        return view('security.devices', compact('devices'));
    }

    // Security Settings
    public function settings()
    {
        $this->ensureAdmin();

        $settings = StoreSetting::firstOrCreate();
        return view('security.settings', compact('settings'));
    }

    // Revoke Device
    public function revokeDevice($id)
    {
        $this->ensureAdmin();

        $device = UserDevice::findOrFail($id);
        $device->update(['is_active' => false]);
        return back()->with('success', 'Device access revoked!');
    }
}
