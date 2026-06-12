@extends('layouts.app')

@section('page-title', 'Discounts')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Discounts</h2>
            <a href="{{ route('sales.discounts.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Create Discount
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
                        <th class="text-left">Name</th>
                        <th class="text-left">Type</th>
                        <th class="text-left">Value</th>
                        <th class="text-left">Min Amount</th>
                        <th class="text-left">Max Amount</th>
                        <th class="text-left">Valid From</th>
                        <th class="text-left">Valid To</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discounts as $discount)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $discount->name }}</td>
                        <td class="text-gray-600">{{ ucfirst($discount->type) }}</td>
                        <td class="text-gray-600">
                            {{ $discount->type == 'percentage' ? $discount->value . '%' : 'TZS ' . number_format($discount->value, 2) }}
                        </td>
                        <td class="text-gray-600">{{ $discount->min_amount ? 'TZS ' . number_format($discount->min_amount, 2) : '-' }}</td>
                        <td class="text-gray-600">{{ $discount->max_amount ? 'TZS ' . number_format($discount->max_amount, 2) : '-' }}</td>
                        <td class="text-gray-600">{{ $discount->start_date ? $discount->start_date->format('M d, Y') : '-' }}</td>
                        <td class="text-gray-600">{{ $discount->end_date ? $discount->end_date->format('M d, Y') : '-' }}</td>
                        <td>
                            <span class="badge {{ $discount->is_active ? 'badge-green' : 'badge-gray' }}">
                                {{ $discount->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('sales.discounts.edit', $discount) }}" class="text-primary-600 hover:text-primary-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('sales.discounts.toggle', $discount) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Toggle">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                            <form action="{{ route('sales.discounts.destroy', $discount) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this discount?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection