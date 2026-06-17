@extends('layouts.app')

@section('page-title', 'Employees')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Employees</h2>
            <a href="{{ route('hr.employees.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Employee
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
                    @if($employees->count() > 0)
                        @foreach($employees as $index => $employee)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $employees->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $employee->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $employee->email }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $employee->phone ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="role-tag role-{{ $employee->role }}">{{ $employee->role }}</span>
                                </td>
                                <td class="px-4 py-3 flex gap-2">
                                    <a href="{{ route('hr.employees.edit', $employee->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form action="{{ route('hr.employees.delete', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?')">
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
                                No employees found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection