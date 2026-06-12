@extends('layouts.app')

@section('page-title', 'Stock Receiving')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Goods Received Notes (GRN)</h2>
            <a href="{{ route('inventory.receiving.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>New GRN
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
                        <th class="text-left">GRN Number</th>
                        <th class="text-left">Supplier</th>
                        <th class="text-left">Date Received</th>
                        <th class="text-left">Total Amount</th>
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grns as $grn)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $grn->grn_number }}</td>
                        <td class="text-gray-600">{{ $grn->supplier->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ $grn->received_date }}</td>
                        <td class="text-gray-600">TZS {{ number_format($grn->total, 2) }}</td>
                        <td>
                            <span class="badge badge-green">Received</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection