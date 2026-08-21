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
                        <th class="text-left">TRA</th>
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
                        <td>
                            @if($sale->tra_status == 'posted')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Posted
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                    <i class="fas fa-minus-circle mr-1"></i>Pending
                                </span>
                            @endif
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('sales.receipts.show', $sale) }}" class="text-primary-600 hover:text-primary-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('sales.receipts.print', $sale) }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Print Invoice">
                                <i class="fas fa-print"></i>
                            </a>
                            <a href="{{ route('sales.receipts.efd-print', $sale) }}" target="_blank" class="text-green-600 hover:text-green-800" title="Print EFD Receipt">
                                <i class="fas fa-receipt"></i>
                            </a>
                            @if($sale->tra_status != 'posted')
                            <button onclick="postSaleToTra({{ $sale->id }}, this)" class="text-orange-600 hover:text-orange-800" title="Post to TRA">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-8">No receipts found.</td>
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
    </div>
</div>

<script>
function postSaleToTra(saleId, btn) {
    // First confirmation dialog
    const firstConfirmed = confirm('Are you sure you want to post this sale to TRA?');
    if (!firstConfirmed) return;

    // Second confirmation dialog
    const secondConfirmed = confirm('Are you REALLY sure you want to post this sale to TRA? This action cannot be undone.');
    if (!secondConfirmed) return;
    
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
    }
    
    fetch('/sales/receipts/post-to-tra', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ sale_id: saleId })
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Sale posted to TRA successfully!');
            location.reload();
        } else {
            alert('TRA posting failed: ' + (result.error || 'Unknown error'));
            if (btn) {
                btn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i>';
                btn.disabled = false;
            }
        }
    })
    .catch(e => {
        alert('Error: ' + e.message);
        if (btn) {
            btn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i>';
            btn.disabled = false;
        }
    });
}
</script>
@endsection
