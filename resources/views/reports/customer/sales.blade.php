@extends('layouts.app')

@section('page-title', 'Customer Sales')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Customer Sales</h2>
            <form method="GET" action="{{ route('reports.customer.sales') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.customer.sales.download', ['start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        @if($customers->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-users text-4xl mb-3"></i>
            <p>No customers found.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Customer</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Email</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Phone</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Purchases</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Total Spent</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50 {{ $customer->purchase_count === 0 ? 'opacity-50' : '' }}">
                        <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                        <td class="px-4 py-3">{{ $customer->email ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $customer->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($customer->purchase_count) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format($customer->total_spent, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Total</td>
                        <td class="px-4 py-3 text-right">{{ number_format($customers->sum('purchase_count')) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($customers->sum('total_spent'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
