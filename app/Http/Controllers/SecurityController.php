<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Models\LoginHistory;
use App\Models\UserDevice;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    // Access Control
    public function access()
    {
        $roles = ['admin', 'cashier', 'manager', 'accountant'];
        $permissions = ['create', 'read', 'update', 'delete'];
        return view('security.access', compact('roles', 'permissions'));
    }

    // Audit Logs
    public function audit()
    {
        $logs = ActionLog::with('user')->latest()->paginate(50);
        return view('security.audit', compact('logs'));
    }

    // Login History
    public function logins()
    {
        $history = LoginHistory::with('user')->latest()->paginate(50);
        return view('security.logins', compact('history'));
    }

    // Device Management
    public function devices()
    {
        $devices = UserDevice::with('user')->latest()->paginate(50);
        return view('security.devices', compact('devices'));
    }

    // Security Settings
    public function settings()
    {
        $settings = StoreSetting::firstOrCreate();
        return view('security.settings', compact('settings'));
    }

    // Revoke Device
    public function revokeDevice($id)
    {
        $device = UserDevice::findOrFail($id);
        $device->update(['is_active' => false]);
        return back()->with('success', 'Device access revoked!');
    }
}
