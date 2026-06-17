@extends('layouts.app')

@section('page-title', 'Roles & Permissions')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Roles & Permissions</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($roles as $role)
                <div class="border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                            <i class="fas fa-user-shield text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ ucfirst($role) }}</h3>
                            <p class="text-sm text-gray-500">System Role</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @foreach($permissions as $module => $actions)
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500 text-xs"></i>
                                <span class="text-sm text-gray-600">{{ ucfirst($module) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 border-t pt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Permissions Overview</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Module</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Permissions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($permissions as $module => $actions)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ ucfirst($module) }}</td>
                                <td class="px-4 py-3">
                                    @foreach($actions as $action)
                                        <span class="inline-block px-2 py-1 bg-gray-100 rounded text-xs text-gray-700 mr-1 mb-1">{{ ucfirst($action) }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
