@extends('layouts.app')

@section('page-title', 'Sales History')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Sales History</h2>
            <a href="{{ route('sales.new') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>New Sale
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
                        <th class="text-left">Invoice #</th>
                        <th class="text-left">Customer</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Total</th>
                        <th class="text-left">Type</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                        </td>
                        <td class="text-gray-600">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td class="text-gray-600">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-gray-600">TZS {{ number_format($sale->total, 2) }}</td>
                        <td class="text-gray-600">{{ ucfirst($sale->type) }}</td>
                        <td>
                            <span class="badge {{ $sale->status == 'completed' ? 'badge-green' : 'badge-red' }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary-600 hover:text-primary-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('sales.receipts.download', $sale) }}" class="text-primary-600 hover:text-primary-800" title="Download PDF">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="{{ route('sales.receipts.print', $sale) }}" class="text-primary-600 hover:text-primary-800" title="Print" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            @if($sale->status == 'completed')
                            <a href="{{ route('sales.returns') }}?sale={{ $sale->id }}" class="text-yellow-600 hover:text-yellow-800" title="Return">
                                <i class="fas fa-undo"></i>
                            </a>
                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this sale?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Cancel">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
