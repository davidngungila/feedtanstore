@extends('layouts.app')

@section('page-title', 'Access Control')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-6 text-primary-900">Access Control</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Role</th>
                        @foreach($permissions as $perm)
                            <th class="px-4 py-3 text-center text-gray-700 font-medium">{{ ucfirst($perm) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($roles as $role)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ ucfirst($role) }}</td>
                            @foreach($permissions as $perm)
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500" {{ $role === 'admin' ? 'checked' : '' }}>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="button" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                Save Permissions
            </button>
        </div>
    </div>
</div>
@endsection