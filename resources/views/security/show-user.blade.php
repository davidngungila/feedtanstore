@extends('layouts.app')

@section('page-title', 'View User')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">View User</h2>
            <a href="{{ route('security.users') }}" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Users
            </a>
        </div>
        
        <!-- User Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <p class="text-gray-900">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <p class="text-gray-900">{{ $user->phone ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ ucfirst($user->role) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Action Logs -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Action Logs</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Time</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Action</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Details</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @if(count($logs) > 0)
                            @foreach($logs as $log)
                                <tr>
                                    <td class="px-4 py-3 text-gray-600">{{ $log['time']->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($log['action'] === 'Login') bg-blue-100 text-blue-800
                                            @elseif($log['action'] === 'Create') bg-green-100 text-green-800
                                            @elseif($log['action'] === 'Update') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif
                                        ">
                                            {{ $log['action'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $log['details'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $log['ip'] }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No action logs found for this user.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
