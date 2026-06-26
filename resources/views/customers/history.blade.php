@extends('layouts.app')

@section('page-title', 'Customer History')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-primary-900">Customer History</h2>
            <form action="{{ route('customers.history') }}" method="GET" id="customerHistorySearchForm" class="w-full md:w-72">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        id="customerHistorySearch"
                        value="{{ $search ?? '' }}"
                        placeholder="Search customer history..."
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
                        <th class="text-left">Customer</th>
                        <th class="text-left">Total Sales</th>
                        <th class="text-left">Total Spent</th>
                        <th class="text-left">Phone</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="customer-history-table-body">
                    @forelse($customers as $customer)
                    <tr data-search="{{ strtolower($customer->name . ' ' . ($customer->email ?? '') . ' ' . ($customer->phone ?? '') . ' ' . ($customer->address ?? '') . ' ' . $customer->sales->count() . ' ' . $customer->sales->sum('total')) }}">
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a>
                        </td>
                        <td class="text-gray-600">{{ $customer->sales->count() }}</td>
                        <td class="text-gray-600">TZS {{ number_format($customer->sales->sum('total'), 2) }}</td>
                        <td class="text-gray-600">{{ $customer->phone ?? '-' }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('customers.show', $customer) }}" class="text-primary-600 hover:text-primary-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-8">No customer history found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <script>
            const customerHistorySearch = document.getElementById('customerHistorySearch');
            const customerHistoryRows = document.querySelectorAll('#customer-history-table-body tr');
            let customerHistorySearchTimer = null;

            if (customerHistorySearch) {
                customerHistorySearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    customerHistoryRows.forEach(row => {
                        const searchData = row.getAttribute('data-search') || '';
                        row.style.display = searchData.includes(searchTerm) ? '' : 'none';
                    });

                    clearTimeout(customerHistorySearchTimer);
                    customerHistorySearchTimer = setTimeout(() => {
                        document.getElementById('customerHistorySearchForm').submit();
                    }, 350);
                });
            }
        </script>
    </div>
</div>
@endsection
