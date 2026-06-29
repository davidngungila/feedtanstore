<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Models\AdminAccessToken;
use App\Models\LoginHistory;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function redirectEntry(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->to($this->createEncryptedEntryUrl());
    }

    public function showEntry(Request $request, string $entryToken)
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }

        $entryTokenHash = hash('sha256', $entryToken);
        $currentTokenId = (int) $request->session()->get('admin_entry_token_id', 0);
        $tokenRecord = null;

        if (
            $this->hasValidEntryGrant($request)
            && $currentTokenId > 0
            && hash_equals((string) $request->session()->get('admin_entry_path_hash', ''), $entryTokenHash)
        ) {
            $tokenRecord = AdminAccessToken::find($currentTokenId);
        }

        if (!$tokenRecord) {
            $token = $this->decryptEntryToken($entryToken);
            if ($token === null) {
                return redirect()->route('home');
            }

            $tokenRecord = AdminAccessToken::query()
                ->where('token_hash', hash('sha256', $token))
                ->first();

            if (!$tokenRecord || $tokenRecord->used_at || !$tokenRecord->expires_at || $tokenRecord->expires_at->isPast()) {
                return redirect()->route('home');
            }

            $tokenRecord->forceFill([
                'used_at' => now(),
                'used_ip' => $request->ip(),
                'used_user_agent' => (string) $request->userAgent(),
            ])->save();
        }

        $expiresAt = $tokenRecord->expires_at;

        $request->session()->put([
            'admin_entry_granted' => true,
            'admin_entry_granted_until' => $expiresAt->timestamp,
            'admin_entry_token_id' => $tokenRecord->id,
            'admin_entry_path_hash' => $entryTokenHash,
        ]);

        $loginAccess = Str::random(64);
        $request->session()->put('admin_login_access_hash', hash('sha256', $loginAccess));

        return view('auth.login', [
            'entryGranted' => true,
            'accessToken' => $loginAccess,
        ]);
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'cashier') {
                return redirect()->intended(route('cashier.dashboard'));
            } elseif ($user->role === 'rider') {
                return redirect()->intended(route('rider.dashboard'));
            }
            return redirect()->intended(route('dashboard'));
        }

        // Always show login form
        return view('auth.login', [
            'entryGranted' => true,
            'accessToken' => '',
        ]);
    }

    public function login(Request $request)
    {
        // Allow login both with entry link and directly
        if ($request->has('access') && !$this->hasValidLoginAccess($request, (string) $request->input('access', ''))) {
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
            } elseif ($user->role === 'rider') {
                return redirect()->intended(route('rider.dashboard'));
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
        return redirect()->route('home');
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
            'admin_entry_token_id',
            'admin_entry_path_hash',
            'admin_login_access_hash',
        ]);
    }

    private function hasValidLoginAccess(Request $request, string $access): bool
    {
        if (!$this->hasValidEntryGrant($request) || $access === '') {
            return false;
        }

        $expectedHash = (string) $request->session()->get('admin_login_access_hash', '');
        if ($expectedHash === '') {
            return false;
        }

        return hash_equals($expectedHash, hash('sha256', $access));
    }

    private function decryptEntryToken(string $entryToken): ?string
    {
        $decoded = base64_decode(strtr($entryToken, '-_', '+/') . str_repeat('=', (4 - strlen($entryToken) % 4) % 4), true);
        if ($decoded === false) {
            return null;
        }

        try {
            return Crypt::decryptString($decoded);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function createEncryptedEntryUrl(int $minutes = 10): string
    {
        $token = Str::random(40);
        $encryptedToken = Crypt::encryptString($token);
        $entryToken = rtrim(strtr(base64_encode($encryptedToken), '+/', '-_'), '=');

        AdminAccessToken::create([
            'token_hash' => hash('sha256', $token),
            'encrypted_token' => $encryptedToken,
            'expires_at' => now()->addMinutes($minutes),
        ]);

        return url('/' . $entryToken);
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
