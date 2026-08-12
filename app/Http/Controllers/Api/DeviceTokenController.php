<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    /**
     * Register (or refresh) the authenticated user's FCM device token.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:512',
            'device_name' => 'nullable|string|max:255',
            'device_type' => 'nullable|in:android,ios,web',
            'app_version' => 'nullable|string|max:50',
        ]);

        $user = $request->user();
        $token = $request->fcm_token;

        $device = UserDevice::where('fcm_token', $token)->first();

        if ($device && $device->user_id !== $user->id) {
            // The token was previously registered to another user; re-home it.
            $device->user_id = $user->id;
        } elseif (! $device) {
            $device = new UserDevice(['user_id' => $user->id]);
        }

        $device->fill([
            'fcm_token' => $token,
            'device_name' => $request->device_name ?? $device->device_name,
            'device_type' => $request->device_type ?? $device->device_type ?? 'android',
            'app_version' => $request->app_version ?? $device->app_version,
            'last_used_at' => now(),
            'is_active' => true,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Device token registered',
            'device' => $device->fresh(),
        ]);
    }

    /**
     * Remove the authenticated user's FCM device token (e.g. on logout).
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:512',
        ]);

        UserDevice::query()
            ->where('user_id', $request->user()->id)
            ->where('fcm_token', $request->fcm_token)
            ->update([
                'is_active' => false,
                'fcm_token' => null,
                'last_used_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Device token removed']);
    }
}
