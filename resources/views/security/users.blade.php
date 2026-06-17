@extends('layouts.app')

@section('page-title', 'User Accounts')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">User Accounts</h2>
            <a href="{{ route('security.users.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add User
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Email</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Phone</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Role</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($users->count() > 0)
                        @foreach($users as $index => $user)
                            <tr>
                                <td class="px-4 py-3 text-gray-600">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->phone ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $user->role }}</span>
                                </td>
                                <td class="px-4 py-3 flex gap-2">
                                    <a href="{{ route('security.users.show', $user->id) }}" class="text-green-600 hover:text-green-800 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                    <a href="{{ route('security.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form action="{{ route('security.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection