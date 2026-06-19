@extends('layouts.app')

@section('page-title', 'Customers')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
            <h2 class="text-xl font-bold text-primary-900">Customers</h2>
            <div class="flex items-center gap-2 flex-1 max-w-md">
                <div class="relative flex-1">
                    <input type="text" id="customerSearch" placeholder="Search customers..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
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
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('customerSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const name = row.querySelector('td:first-child').textContent.toLowerCase();
        const email = (row.querySelector('td:nth-child(2)').textContent || '').toLowerCase();
        const phone = (row.querySelector('td:nth-child(3)').textContent || '').toLowerCase();
        
        if (name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endsection
