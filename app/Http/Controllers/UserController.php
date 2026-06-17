<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('security.users', compact('users'));
    }

    public function create()
    {
        return view('security.create-user');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string',
            'role' => 'required|string'
        ]);

        $userData = $request->all();
        $userData['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_image')) {
            $userData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        User::create($userData);
        return redirect()->route('security.users')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('security.edit-user', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string',
            'role' => 'required|string',
            'password' => 'nullable|min:6'
        ]);

        $userData = $request->except(['password']);
        
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::delete('public/' . $user->profile_image);
            }
            $userData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }
        
        $user->update($userData);
        
        return redirect()->route('security.users')->with('success', 'User updated successfully!');
    }

    public function show($id)
    {
        $user = User::with(['actionLogs', 'loginHistory', 'devices'])->findOrFail($id);
        return view('security.show-user', compact('user'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->profile_image) {
            Storage::delete('public/' . $user->profile_image);
        }
        
        $user->delete();
        
        return redirect()->route('security.users')->with('success', 'User deleted successfully!');
    }
}
