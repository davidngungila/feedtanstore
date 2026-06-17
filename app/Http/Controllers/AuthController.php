<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Models\LoginHistory;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
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
