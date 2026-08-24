@extends('layouts.app')

@section('page-title', 'Goods Received Notes')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary-900">Goods Received Notes (GRN)</h1>
            <p class="text-gray-600">Record and track all goods received into the store</p>
        </div>
        <a href="{{ route('storekeeper.grn.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>New GRN
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('storekeeper.grn') }}" class="mb-4">
        <div class="relative max-w-md">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by GRN number, supplier or PO..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </form>

    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">GRN Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Purchase Order</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Received Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($grns as $grn)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $grn->grn_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $grn->supplier->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $grn->purchaseOrder->po_number ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($grn->received_date)->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium">{{ number_format($grn->total, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    {{ ucfirst($grn->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('storekeeper.grn.show', $grn) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 block text-gray-300"></i>
                                No goods received notes found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
