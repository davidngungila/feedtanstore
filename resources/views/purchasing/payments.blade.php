@extends('layouts.app')

@section('page-title', 'Supplier Payments')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-primary-900">Supplier Payments</h2>
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <form action="{{ route('purchasing.payments') }}" method="GET" id="supplierPaymentSearchForm" class="w-full md:w-72">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            id="supplierPaymentSearch"
                            value="{{ $search ?? '' }}"
                            placeholder="Search payments..."
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            autocomplete="off"
                        >
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <a href="{{ route('purchasing.payments.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>New Payment
                </a>
            </div>
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
                        <th class="text-left">Payment Number</th>
                        <th class="text-left">Supplier</th>
                        <th class="text-left">Amount</th>
                        <th class="text-left">Method</th>
                        <th class="text-left">Transaction ID</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="supplier-payments-table-body">
                    @forelse($payments as $payment)
                    <tr data-search="{{ strtolower($payment->payment_number . ' ' . ($payment->supplier->name ?? '') . ' ' . ($payment->purchaseOrder->po_number ?? '') . ' ' . ($payment->payment_method ?? '') . ' ' . ($payment->transaction_id ?? '') . ' ' . ($payment->status ?? '') . ' ' . ($payment->payment_date ?? '') . ' ' . $payment->amount) }}">
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('purchasing.payments.show', $payment) }}" class="hover:underline">{{ $payment->payment_number }}</a>
                        </td>
                        <td class="text-gray-600">{{ $payment->supplier->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">TZS {{ number_format($payment->amount, 2) }}</td>
                        <td class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="text-gray-600">{{ $payment->transaction_id ?? '-' }}</td>
                        <td class="text-gray-600">{{ $payment->payment_date ? date('M d, Y', strtotime($payment->payment_date)) : '-' }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('purchasing.payments.download', $payment) }}" class="text-green-600 hover:text-green-800 p-1" title="Download PDF">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="{{ route('purchasing.payments.show', $payment) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('purchasing.payments.edit', $payment) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('purchasing.payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500 py-8">No supplier payments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <script>
            const supplierPaymentSearch = document.getElementById('supplierPaymentSearch');
            const supplierPaymentRows = document.querySelectorAll('#supplier-payments-table-body tr');
            let supplierPaymentSearchTimer = null;

            if (supplierPaymentSearch) {
                supplierPaymentSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    supplierPaymentRows.forEach(row => {
                        const searchData = row.getAttribute('data-search') || '';
                        row.style.display = searchData.includes(searchTerm) ? '' : 'none';
                    });

                    clearTimeout(supplierPaymentSearchTimer);
                    supplierPaymentSearchTimer = setTimeout(() => {
                        document.getElementById('supplierPaymentSearchForm').submit();
                    }, 350);
                });
            }
        </script>
    </div>
</div>
@endsection
