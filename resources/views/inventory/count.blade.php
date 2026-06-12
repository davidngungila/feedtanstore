@extends('layouts.app')

@section('page-title', 'Stock Counts')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Stock Counts</h2>
            <a href="{{ route('inventory.count.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>New Count
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
                        <th class="text-left">Count Number</th>
                        <th class="text-left">Location</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($counts as $count)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $count->count_number }}</td>
                        <td class="text-gray-600">{{ $count->location->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ $count->count_date }}</td>
                        <td>
                            <span class="badge badge-green">Completed</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection