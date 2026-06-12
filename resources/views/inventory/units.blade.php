@extends('layouts.app')

@section('page-title', 'Units of Measure')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Units of Measure</h2>
            <a href="{{ route('inventory.units.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Unit
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Name</th>
                        <th class="text-left">Short Name</th>
                        <th class="text-left">Description</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($units as $unit)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('inventory.units.show', $unit) }}" class="hover:underline">{{ $unit->name }}</a>
                        </td>
                        <td class="text-gray-600">{{ $unit->short_name }}</td>
                        <td class="text-gray-600">{{ $unit->description ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $unit->is_active ? 'badge-green' : 'badge-gray' }}">
                                {{ $unit->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('inventory.units.show', $unit) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('inventory.units.edit', $unit) }}" class="text-primary-600 hover:text-primary-800 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('inventory.units.destroy', $unit) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this unit?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection