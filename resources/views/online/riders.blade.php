@extends('layouts.app')

@section('page-title', 'Delivery Riders')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Delivery Riders</h2>
            <a href="{{ route('online.riders.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Rider
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Name</th>
                        <th class="px-4 py-3 text-left text-gray-700">Phone</th>
                        <th class="px-4 py-3 text-left text-gray-700">Vehicle Type</th>
                        <th class="px-4 py-3 text-left text-gray-700">Plate No.</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($riders as $rider)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $rider->name }}</td>
                        <td class="px-4 py-3">{{ $rider->phone }}</td>
                        <td class="px-4 py-3">{{ $rider->vehicle_type ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $rider->vehicle_plate ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($rider->is_active) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $rider->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('online.riders.edit', $rider) }}" class="text-primary-600 hover:text-primary-800 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('online.riders.destroy', $rider) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this rider?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No delivery riders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection