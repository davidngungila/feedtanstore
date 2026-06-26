@extends('layouts.app')

@section('page-title', 'Receipts')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-primary-900">Receipts</h2>
            <form action="{{ route('sales.receipts') }}" method="GET" id="receiptSearchForm" class="w-full md:w-72">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        id="receiptSearch"
                        value="{{ $search ?? '' }}"
                        placeholder="Search receipts..."
                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        autocomplete="off"
                    >
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Invoice #</th>
                        <th class="text-left">Customer</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Total</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="receipts-table-body">
                    @forelse($sales as $sale)
                    <tr data-search="{{ strtolower($sale->invoice_number . ' ' . ($sale->customer->name ?? 'Walk-in') . ' ' . ($sale->customer->phone ?? '') . ' ' . ($sale->customer->email ?? '') . ' ' . ($sale->user->name ?? '') . ' ' . ($sale->status ?? '') . ' ' . ($sale->type ?? '') . ' ' . $sale->total . ' ' . $sale->created_at->format('M d, Y H:i')) }}">
                        <td class="font-medium text-primary-900">{{ $sale->invoice_number }}</td>
                        <td class="text-gray-600">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td class="text-gray-600">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-gray-600">TZS {{ number_format($sale->total, 2) }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary-600 hover:text-primary-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-8">No receipts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="mt-6">
                {{ $sales->links() }}
            </div>
        @endif

        <script>
            const receiptSearch = document.getElementById('receiptSearch');
            const receiptRows = document.querySelectorAll('#receipts-table-body tr');
            let receiptSearchTimer = null;

            if (receiptSearch) {
                receiptSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    receiptRows.forEach(row => {
                        const searchData = row.getAttribute('data-search') || '';
                        row.style.display = searchData.includes(searchTerm) ? '' : 'none';
                    });

                    clearTimeout(receiptSearchTimer);
                    receiptSearchTimer = setTimeout(() => {
                        document.getElementById('receiptSearchForm').submit();
                    }, 350);
                });
            }
        </script>
    </div>
</div>
@endsection
