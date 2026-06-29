<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Models\AdminAccessToken;
use App\Models\StoreSetting;

class DeliveryRiderController extends Controller
{
    public function index()
    {
        $riders = DeliveryRider::with(['user', 'latestLocation'])->get();
        $storeSettings = \App\Models\StoreSetting::firstOrCreate();
        $storeLat = $storeSettings->store_latitude ?? -3.3869;
        $storeLng = $storeSettings->store_longitude ?? 36.6883;
        return view('online.riders', compact('riders', 'storeLat', 'storeLng'));
    }

    public function create()
    {
        return view('online.riders-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:255|unique:users,phone',
            'vehicle_type' => 'nullable|string|max:255',
            'vehicle_plate' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        \DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'rider',
            ]);

            DeliveryRider::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'vehicle_type' => $request->vehicle_type,
                'vehicle_plate' => $request->vehicle_plate,
                'is_active' => $request->has('is_active'),
                'user_id' => $user->id,
            ]);

            \DB::commit();
            return redirect()->route('online.riders')->with('success', 'Delivery Rider created successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to create rider: ' . $e->getMessage());
        }
    }

    public function edit(DeliveryRider $rider)
    {
        $rider->load(['user.devices', 'locations' => function($q) {
            $q->latest()->take(10);
        }]);
        $storeSettings = \App\Models\StoreSetting::firstOrCreate();
        return view('online.riders-edit', compact('rider', 'storeSettings'));
    }

    public function update(Request $request, DeliveryRider $rider)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $rider->user_id,
            'password' => 'nullable|string|min:8',
            'phone' => 'required|string|max:255|unique:users,phone,' . $rider->user_id,
            'vehicle_type' => 'nullable|string|max:255',
            'vehicle_plate' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        \DB::beginTransaction();
        try {
            if ($rider->user) {
                $userData = [
                    'name' => $request->name,
                    'phone' => $request->phone,
                ];
                if ($request->email) {
                    $userData['email'] = $request->email;
                }
                if ($request->password) {
                    $userData['password'] = Hash::make($request->password);
                }
                $rider->user->update($userData);
            }

            $rider->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'vehicle_type' => $request->vehicle_type,
                'vehicle_plate' => $request->vehicle_plate,
                'is_active' => $request->has('is_active'),
            ]);

            \DB::commit();
            return redirect()->route('online.riders')->with('success', 'Delivery Rider updated successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to update rider: ' . $e->getMessage());
        }
    }

    public function toggleActive(DeliveryRider $rider)
    {
        $rider->update(['is_active' => !$rider->is_active]);
        return redirect()->route('online.riders')->with('success', 'Rider status updated successfully!');
    }

    public function destroy(DeliveryRider $rider)
    {
        \DB::beginTransaction();
        try {
            if ($rider->user) {
                $rider->user->delete();
            }
            $rider->delete();
            \DB::commit();
            return redirect()->route('online.riders')->with('success', 'Delivery Rider deleted successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to delete rider: ' . $e->getMessage());
        }
    }

    public function generateEntryLink(DeliveryRider $rider)
    {
        $minutes = 60 * 24; // 24 hours
        $settings = StoreSetting::firstOrCreate();
        $token = Str::random(40);
        $encryptedToken = Crypt::encryptString($token);
        $entryToken = rtrim(strtr(base64_encode($encryptedToken), '+/', '-_'), '=');

        AdminAccessToken::create([
            'token_hash' => hash('sha256', $token),
            'encrypted_token' => $encryptedToken,
            'expires_at' => now()->addMinutes($minutes),
        ]);

        $baseUrl = $settings->store_url ?? config('app.url');
        $url = rtrim($baseUrl, '/') . '/' . $entryToken;

        return back()->with('success', 'Entry link generated! Link: ' . $url);
    }
}