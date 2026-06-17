@extends('layouts.app')

@section('page-title', 'Device Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-6 text-primary-900">Device Management</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">User</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Device Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Type</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Browser</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Last Active</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($devices->count() > 0)
                        @foreach($devices as $device)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $device->user ? $device->user->name : 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $device->device_name ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $device->device_type ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $device->browser ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $device->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $device->is_active ? 'Active' : 'Revoked' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($device->is_active)
                                        <form action="{{ route('security.devices.revoke', $device->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to revoke access to this device?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                Revoke Access
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No devices found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $devices->links() }}
        </div>
    </div>
</div>
@endsection