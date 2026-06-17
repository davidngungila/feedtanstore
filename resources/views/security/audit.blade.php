@extends('layouts.app')

@section('page-title', 'Audit Logs')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Audit Logs</h2>
            <div class="flex gap-2">
                <input type="text" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <button class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium transition-colors">
                    Filter
                </button>
            </div>
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
                    <tr>
                        <td class="px-4 py-3 text-gray-600">{{ now()->subHour()->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">System</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Login</span></td>
                        <td class="px-4 py-3 text-gray-600">System audit</td>
                        <td class="px-4 py-3 text-gray-600">127.0.0.1</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-600">{{ now()->subHours(2)->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">Admin</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Create</span></td>
                        <td class="px-4 py-3 text-gray-600">Created new product</td>
                        <td class="px-4 py-3 text-gray-600">192.168.1.1</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection