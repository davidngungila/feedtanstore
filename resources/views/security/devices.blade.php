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
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Device Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Type</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Browser</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Last Active</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">Office Desktop</td>
                        <td class="px-4 py-3 text-gray-600">Desktop</td>
                        <td class="px-4 py-3 text-gray-600">Chrome 120</td>
                        <td class="px-4 py-3 text-gray-600">{{ now()->subMinutes(5)->diffForHumans() }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span></td>
                        <td class="px-4 py-3">
                            <button class="text-red-600 hover:text-red-800 font-medium text-sm">
                                Revoke Access
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">Home Laptop</td>
                        <td class="px-4 py-3 text-gray-600">Laptop</td>
                        <td class="px-4 py-3 text-gray-600">Firefox 119</td>
                        <td class="px-4 py-3 text-gray-600">{{ now()->subDays(2)->diffForHumans() }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span></td>
                        <td class="px-4 py-3">
                            <button class="text-red-600 hover:text-red-800 font-medium text-sm">
                                Revoke Access
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection