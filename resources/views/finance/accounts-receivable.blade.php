@extends('layouts.app')

@section('page-title', 'Accounts Receivable')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Accounts Receivable</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600">Invoice</th>
                        <th class="px-4 py-3 text-left text-gray-600">Customer</th>
                        <th class="px-4 py-3 text-left text-gray-600">Total</th>
                        <th class="px-4 py-3 text-left text-gray-600">Paid</th>
                        <th class="px-4 py-3 text-left text-gray-600">Due</th>
                        <th class="px-4 py-3 text-left text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($sales as $sale)
                        @php
                            $due = $sale->total - $sale->paid;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-3">{{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}</td>
                            <td class="px-4 py-3 font-bold">TZS {{ number_format($sale->total, 2) }}</td>
                            <td class="px-4 py-3 font-bold text-green-700">TZS {{ number_format($sale->paid, 2) }}</td>
                            <td class="px-4 py-3 font-bold text-red-700">TZS {{ number_format($due, 2) }}</td>
                            <td class="px-4 py-3">{{ $sale->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('sales.show', $sale->id) }}" class="text-blue-600 hover:text-blue-800">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No outstanding accounts receivable!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection