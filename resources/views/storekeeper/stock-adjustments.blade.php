@extends('layouts.app')

@section('page-title', 'Stock Adjustments')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary-900">Stock Adjustments</h1>
            <p class="text-gray-600">Manual stock adjustments and corrections</p>
        </div>
        <a href="{{ route('storekeeper.stock-adjustments.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>New Adjustment
        </a>
    </div>

    <!-- Adjustment Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Adjustments</p>
                    <p class="text-3xl font-bold text-primary-600">{{ $adjustments->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-sliders-h text-primary-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Additions</p>
                    <p class="text-3xl font-bold text-green-600">{{ $adjustments->where('quantity_change', '>', 0)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-plus text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Subtractions</p>
                    <p class="text-3xl font-bold text-red-600">{{ $adjustments->where('quantity_change', '<', 0)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-minus text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Adjustments Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Before</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Change</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">After</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($adjustments as $adjustment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $adjustment->reference_number }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $adjustment->product->name }}</p>
                            <p class="text-sm text-gray-500">{{ $adjustment->product->sku }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $adjustment->quantity_before }} {{ $adjustment->product->unit?->short_name ?? 'pcs' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold {{ $adjustment->quantity_change > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $adjustment->quantity_change > 0 ? '+' : '' }}{{ $adjustment->quantity_change }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $adjustment->quantity_after }} {{ $adjustment->product->unit?->short_name ?? 'pcs' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $adjustment->quantity_change > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $adjustment->quantity_change > 0 ? 'Addition' : 'Subtraction' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $adjustment->reason }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $adjustment->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $adjustments->links() }}
        </div>
    </div>
</div>
@endsection
