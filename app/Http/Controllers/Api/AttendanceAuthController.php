<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AttendanceAuthController extends Controller
{
    /**
     * Login with email OR phone
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // email or phone
            'password' => 'required|string',
            // Support legacy keys
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        // Support multiple login payload styles:
        // { "login": "email/phone", "password": "..." }
        // { "email": "...", "password": "..." }
        // { "phone": "...", "password": "..." }
        $login = $request->input('login') ?? $request->input('email') ?? $request->input('phone');

        // Determine if email or phone
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($field, $login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Optionally check if user is active? If role check needed, allow all roles for attendance
        // But require that user is not blocked: is_active check if deliveryRider exists? For generic staff, allow all.
        if ($user->deliveryRider && !$user->deliveryRider->is_active) {
            throw ValidationException::withMessages([
                'login' => ['Your account has been deactivated. Contact support.'],
            ]);
        }

        $token = $user->createToken('attendance-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
                'position' => $user->position,
                'role' => $user->role,
                'profile_image' => $user->profile_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_image) : null,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Forgot password - sends reset link
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent to your email']);
        }

        return response()->json(['message' => 'Unable to send reset link'], 500);
    }

    /**
     * Logout - revoke current token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'employee_id' => $user->employee_id,
            'department' => $user->department,
            'position' => $user->position,
            'role' => $user->role,
            'profile_image' => $user->profile_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_image) : null,
            'email_verified_at' => $user->email_verified_at,
        ]);
    }
}
