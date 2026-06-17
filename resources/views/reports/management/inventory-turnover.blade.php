@extends('layouts.app')

@section('page-title', 'Inventory Turnover')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Inventory Turnover</h2>
            <div class="flex items-center gap-3">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-4 py-2" id="start-date-filter">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-4 py-2" id="end-date-filter">
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="#" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Category</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">COGS</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Turnover Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($category->cogs, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">
                            <span class="{{ $category->turnover < 2 ? 'text-red-700' : ($category->turnover < 4 ? 'text-yellow-700' : 'text-green-700') }}">
                                {{ number_format($category->turnover, 2) }}x
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterReport() {
    const startDate = document.getElementById('start-date-filter').value;
    const endDate = document.getElementById('end-date-filter').value;
    window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
}
</script>
@endsection