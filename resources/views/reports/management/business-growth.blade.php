@extends('layouts.app')

@section('page-title', 'Business Growth')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Business Growth (Last 12 Months)</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Month</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Sales</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Profit</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Profit Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($salesData as $data)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $data['month'] }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($data['sales'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-700 font-semibold">TZS {{ number_format($data['profit'], 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            {{ $data['sales'] > 0 ? number_format(($data['profit'] / $data['sales']) * 100, 2) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
