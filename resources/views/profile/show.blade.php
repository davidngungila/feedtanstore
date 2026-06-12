@extends('layouts.app')

@section('page-title', 'My Profile')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">My Profile</h2>
        
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <div class="flex flex-col items-center">
                        <div class="mb-4">
                            @if(auth()->user()->profile_image)
                                <img src="{{ Storage::url(auth()->user()->profile_image) }}" 
                                    alt="Profile Image" 
                                    class="w-32 h-32 rounded-full object-cover border-4 border-primary-200">
                            @else
                                <div class="w-32 h-32 rounded-full bg-primary-200 flex items-center justify-center border-4 border-primary-200">
                                    <i class="fa-solid fa-user text-4xl text-primary-700"></i>
                                </div>
                            @endif
                        </div>
                        <div class="w-full">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="profile_image" class="form-input w-full">
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" class="form-input w-full" required>
                </div>
                
                <div>
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" class="form-input w-full" required>
                </div>
                
                <div>
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" value="{{ auth()->user()->phone }}" class="form-input w-full">
                </div>
                
                <div class="md:col-span-2">
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
                        <i class="fa-solid fa-save mr-2"></i>Save Profile
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <div class="card rounded-2xl p-6">
        <h3 class="text-xl font-bold text-primary-900 mb-6">Change Password</h3>
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-input w-full" required>
                </div>
                
                <div>
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-input w-full" required>
                </div>
                
                <div>
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-input w-full" required>
                </div>
                
                <div class="md:col-span-2">
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
                        <i class="fa-solid fa-key mr-2"></i>Update Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
