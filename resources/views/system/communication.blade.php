@extends('layouts.app')

@section('page-title', 'Communication Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <!-- Messaging Service API Settings -->
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Messaging Service API V2 Settings</h2>
        
        <form method="POST" action="{{ route('system.update') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">API Key</label>
                    <input type="text" name="sms_api_key" class="form-input input-field" value="{{ old('sms_api_key', $settings->sms_api_key) }}" placeholder="Enter your Messaging Service API Key">
                </div>
                <div>
                    <label class="form-label">Sender ID</label>
                    <input type="text" name="messaging_sender_id" class="form-input input-field" value="{{ old('messaging_sender_id', $settings->messaging_sender_id) }}" placeholder="TANZANIATIP">
                </div>
            </div>
            
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Save Settings
            </button>
        </form>
    </div>
    
    <!-- Communication Profiles -->
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Communication Profiles</h2>
            <a href="{{ route('system.communication-profiles.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Profile
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Name</th>
                        <th class="text-left">Type</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Created At</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($communicationProfiles as $profile)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('system.communication-profiles.show', $profile) }}" class="hover:underline">{{ $profile->name }}</a>
                        </td>
                        <td class="text-gray-600">
                            <span class="badge {{ $profile->type === 'email' ? 'badge-blue' : 'badge-purple' }}">
                                {{ ucfirst($profile->type) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $profile->is_active ? 'badge-green' : 'badge-gray' }}">
                                {{ $profile->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-gray-600">{{ $profile->created_at->format('M d, Y') }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('system.communication-profiles.show', $profile) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('system.communication-profiles.edit', $profile) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('system.communication-profiles.destroy', $profile) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this communication profile?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-8">
                            <i class="fas fa-envelope text-4xl mb-2 opacity-50"></i>
                            <p class="text-lg">No communication profiles yet. Create your first one!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection