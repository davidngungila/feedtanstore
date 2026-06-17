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
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">IP Address</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">User Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-3 text-gray-600">{{ now()->subMinutes(15)->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">Admin</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Success</span></td>
                        <td class="px-4 py-3 text-gray-600">127.0.0.1</td>
                        <td class="px-4 py-3 text-gray-600">Chrome 120 on Windows</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-600">{{ now()->subHours(2)->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">Unknown</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Failed</span></td>
                        <td class="px-4 py-3 text-gray-600">192.168.1.100</td>
                        <td class="px-4 py-3 text-gray-600">Firefox 119 on macOS</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection