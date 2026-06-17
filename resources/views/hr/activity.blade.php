@extends('layouts.app')

@section('page-title', 'Activity Logs')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Activity Logs</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">User</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Action</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Details</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">IP Address</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date/Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($logs->count() > 0)
                        @foreach($logs as $index => $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $logs->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $log->user ? $log->user->name : 'System' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $log->action }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $log->details }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $log->ip_address ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No activity logs found.
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
