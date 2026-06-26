<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Models\LoginHistory;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    public function showEntry(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }

        $hasValidSignature = URL::hasValidSignature($request) || URL::hasValidSignature($request, false);
        if (!$hasValidSignature) {
            abort(403, 'Invalid signature.');
        }

        $token = (string) $request->query('token', '');
        if ($token === '') {
            abort(403, 'Invalid entry token.');
        }

        $expiresAt = now()->addMinutes(10);
        if ($request->has('expires')) {
            $expiresAt = now()->setTimestamp((int) $request->query('expires'));
        }

        $cacheKey = 'admin-entry-token:' . hash('sha256', $token);
        if (!Cache::add($cacheKey, true, $expiresAt)) {
            abort(403, 'This entry link has already been used.');
        }

        $request->session()->put([
            'admin_entry_granted' => true,
            'admin_entry_granted_until' => $expiresAt->timestamp,
        ]);

        return redirect()->route('login');
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return redirect()->intended($user->role === 'cashier' ? route('cashier.dashboard') : route('dashboard'));
        }

        return view('auth.login', [
            'entryGranted' => $this->hasValidEntryGrant(request()),
        ]);
    }

    public function login(Request $request)
    {
        if (!$this->hasValidEntryGrant($request)) {
            abort(403, 'A valid signed entry link is required.');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $deviceName = $request->header('User-Agent');
        $deviceType = $this->getDeviceType($deviceName);
        $browser = $this->getBrowser($deviceName);
        $ipAddress = $request->ip();
        $userAgent = $deviceName;

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            $request->session()->regenerate();
            $this->clearEntryGrant($request);

            // Record successful login
            LoginHistory::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'success' => true,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            // Record action log
            ActionLog::create([
                'user_id' => $user->id,
                'action' => 'Login',
                'details' => 'Successful login to system',
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            // Record or update user device
            UserDevice::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                ],
                [
                    'device_name' => $deviceName,
                    'device_type' => $deviceType,
                    'browser' => $browser,
                    'last_active_at' => now(),
                    'is_active' => true,
                ]
            );
            
            if ($user->role === 'cashier') {
                return redirect()->intended(route('cashier.dashboard'));
            }
            
            return redirect()->intended(route('dashboard'));
        }

        // Record failed login
        LoginHistory::create([
            'user_id' => null,
            'email' => $credentials['email'],
            'success' => false,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Record logout action
        if (Auth::check()) {
            ActionLog::create([
                'user_id' => Auth::id(),
                'action' => 'Logout',
                'details' => 'User logged out of system',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function hasValidEntryGrant(Request $request): bool
    {
        if (!$request->session()->get('admin_entry_granted')) {
            return false;
        }

        $grantedUntil = (int) $request->session()->get('admin_entry_granted_until', 0);
        if ($grantedUntil <= now()->timestamp) {
            $this->clearEntryGrant($request);
            return false;
        }

        return true;
    }

    private function clearEntryGrant(Request $request): void
    {
        $request->session()->forget([
            'admin_entry_granted',
            'admin_entry_granted_until',
        ]);
    }

    private function getDeviceType($userAgent): string
    {
        if (preg_match('/mobile/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    private function getBrowser($userAgent): string
    {
        if (preg_match('/chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/edge/i', $userAgent)) {
            return 'Edge';
        } else {
            return 'Unknown';
        }
    }
}
