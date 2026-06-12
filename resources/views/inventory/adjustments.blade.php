@extends('layouts.app')

@section('page-title', 'Stock Adjustments')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Stock Adjustments</h2>
            <a href="{{ route('inventory.adjustments.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>New Adjustment
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
                        <th class="text-left">Ref. Number</th>
                        <th class="text-left">Product</th>
                        <th class="text-left">Type</th>
                        <th class="text-left">Qty Before</th>
                        <th class="text-left">Change</th>
                        <th class="text-left">Qty After</th>
                        <th class="text-left">Reason</th>
                        <th class="text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adjustments as $adj)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $adj->reference_number }}</td>
                        <td class="text-gray-600">{{ $adj->product->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $adj->type === 'addition' ? 'badge-green' : 'badge-red' }}">
                                {{ $adj->type === 'addition' ? 'Addition' : 'Subtraction' }}
                            </span>
                        </td>
                        <td class="text-gray-600">{{ $adj->quantity_before }}</td>
                        <td class="font-semibold {{ $adj->type === 'addition' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $adj->quantity_change > 0 ? '+' : '' }}{{ $adj->quantity_change }}
                        </td>
                        <td class="text-gray-600">{{ $adj->quantity_after }}</td>
                        <td class="text-gray-600">{{ $adj->reason }}</td>
                        <td class="text-gray-600">{{ $adj->adjustment_date }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection