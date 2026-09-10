<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceProfileController extends Controller
{
    /**
     * Get full profile - Name, Employee ID, Department, Position, Phone, Profile photo
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // Also get attendance stats for profile header
        $totalAttendances = $user->attendances()->whereIn('status', ['present','late','half-day'])->count();
        $totalLeaves = $user->leaves()->where('status','approved')->count();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'employee_id' => $user->employee_id ?? 'EMP-'.str_pad($user->id, 4, '0', STR_PAD_LEFT),
            'department' => $user->department,
            'position' => $user->position,
            'role' => $user->role,
            'profile_image' => $user->profile_image ? Storage::disk('public')->url($user->profile_image) : null,
            'profile_image_path' => $user->profile_image,
            'stats' => [
                'total_present' => $totalAttendances,
                'total_leaves' => $totalLeaves,
            ],
            'created_at' => $user->created_at,
        ]);
    }

    /**
     * Update profile - Name, Phone, Department, Position
     * Employee ID is read-only
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:users,phone,'.$user->id,
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
        ]);

        $user->update($request->only(['name','phone','department','position','email']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
                'position' => $user->position,
                'profile_image' => $user->profile_image ? Storage::disk('public')->url($user->profile_image) : null,
            ]
        ]);
    }

    /**
     * Update profile photo - multipart/form-data image=file OR remove=true
     */
    public function updatePhoto(Request $request)
    {
        $user = $request->user();

        if ($request->boolean('remove')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
                $user->update(['profile_image' => null]);
            }
            return response()->json(['message' => 'Profile photo removed', 'profile_image' => null]);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $file = $request->file('photo') ?? $request->file('image');

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $path = $file->store('profile-images', 'public');
        $user->update(['profile_image' => $path]);

        return response()->json([
            'message' => 'Profile photo updated',
            'profile_image' => Storage::disk('public')->url($path),
            'path' => $path,
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully']);
    }
}
