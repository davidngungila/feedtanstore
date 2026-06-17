@extends('layouts.app')

@section('page-title', 'Audit Logs')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Audit Logs</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Time</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">User</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Action</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Details</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($logs->count() > 0)
                        @foreach($logs as $log)
                            <tr>
                                <td class="px-4 py-3 text-gray-600">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $log->user ? $log->user->name : 'System' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($log->action === 'Login') bg-blue-100 text-blue-800
                                        @elseif($log->action === 'Create') bg-green-100 text-green-800
                                        @elseif($log->action === 'Update') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $log->details }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $log->ip_address }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No audit logs found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection