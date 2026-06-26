@extends('layouts.app')

@section('page-title', 'Customers')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <h2 class="text-xl font-bold text-primary-900">Customers</h2>
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto md:max-w-none">
                <form action="{{ route('customers.list') }}" method="GET" id="customerSearchForm" class="w-full md:w-72">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            id="customerSearch"
                            value="{{ $search ?? '' }}"
                            placeholder="Search customers..."
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            autocomplete="off"
                        >
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <a href="{{ route('customers.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-plus mr-2"></i>New Customer
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
                        <th class="text-left">Name</th>
                        <th class="text-left">Email</th>
                        <th class="text-left">Phone</th>
                        <th class="text-left">Balance</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="customers-table-body">
                    @forelse($customers as $customer)
                    <tr data-search="{{ strtolower($customer->name . ' ' . ($customer->email ?? '') . ' ' . ($customer->phone ?? '') . ' ' . ($customer->address ?? '') . ' ' . ($customer->group->name ?? '') . ' ' . $customer->balance) }}">
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a>
                        </td>
                        <td class="text-gray-600">{{ $customer->email ?? '-' }}</td>
                        <td class="text-gray-600">{{ $customer->phone ?? '-' }}</td>
                        <td class="text-gray-600">TZS {{ number_format($customer->balance, 2) }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('customers.show', $customer) }}" class="text-primary-600 hover:text-primary-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('customers.edit', $customer) }}" class="text-primary-600 hover:text-primary-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-8">No customers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const customerSearch = document.getElementById('customerSearch');
const customerRows = document.querySelectorAll('#customers-table-body tr');
let customerSearchTimer = null;

if (customerSearch) {
    customerSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();

        customerRows.forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            row.style.display = searchData.includes(searchTerm) ? '' : 'none';
        });

        clearTimeout(customerSearchTimer);
        customerSearchTimer = setTimeout(() => {
            document.getElementById('customerSearchForm').submit();
        }, 350);
    });
}
</script>
@endsection
