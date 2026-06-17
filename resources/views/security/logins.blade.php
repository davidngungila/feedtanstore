@extends('layouts.app')

@section('page-title', 'Login History')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Login History</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Time</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">User</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Email</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">IP Address</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">User Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($history->count() > 0)
                        @foreach($history as $entry)
                            <tr>
                                <td class="px-4 py-3 text-gray-600">{{ $entry->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $entry->user ? $entry->user->name : 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $entry->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $entry->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $entry->success ? 'Success' : 'Failed' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $entry->ip_address }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $entry->user_agent }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No login history found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $history->links() }}
        </div>
    </div>
</div>
@endsection