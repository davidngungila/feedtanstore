@extends('layouts.app')

@section('page-title', 'Hourly Sales')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Hourly Sales</h2>
            <form method="GET" action="{{ route('reports.sales.hourly') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="date" value="{{ request('date', $date) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.sales.hourly.download', ['date' => request('date', $date)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        @if($sales->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-chart-bar text-4xl mb-3"></i>
            <p>No sales recorded on this date.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Hour</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Transactions</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Total Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($sales as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ str_pad($row->hour, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad($row->hour, 2, '0', STR_PAD_LEFT) }}:59</td>
                        <td class="px-4 py-3 text-right">{{ number_format($row->count) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format($row->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td class="px-4 py-3">Total</td>
                        <td class="px-4 py-3 text-right">{{ number_format($sales->sum('count')) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($sales->sum('total'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
