@extends('layouts.app')

@section('page-title', 'Shifts')

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
            <h2 class="text-xl font-bold text-primary-900">Work Shifts</h2>
            <a href="{{ route('hr.shifts.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Shift
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Start Time</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">End Time</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Description</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($shifts->count() > 0)
                        @foreach($shifts as $index => $shift)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $shifts->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $shift->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $shift->description ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('hr.shifts.toggle', $shift->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-full text-xs font-semibold {{ $shift->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $shift->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 flex gap-2">
                                    <a href="{{ route('hr.shifts.edit', $shift->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form action="{{ route('hr.shifts.delete', $shift->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this shift?')">
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
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No shifts found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $shifts->links() }}
        </div>
    </div>
</div>
@endsection
