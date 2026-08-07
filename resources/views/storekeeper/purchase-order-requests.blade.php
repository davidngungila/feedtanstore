@extends('layouts.app')

@section('page-title', 'Stock Requests')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary-900">Stock Requests</h1>
            <p class="text-gray-600">Request new stock purchases</p>
        </div>
        <a href="{{ route('storekeeper.purchase-order-requests.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>New Request
        </a>
    </div>

    <!-- Request Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $requests->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Approved</p>
                    <p class="text-3xl font-bold text-green-600">{{ $requests->where('status', 'approved')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Rejected</p>
                    <p class="text-3xl font-bold text-red-600">{{ $requests->where('status', 'rejected')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Processed</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $requests->where('status', 'processed')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Request Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estimated Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($requests as $request)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $request->request_number }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $request->product->name }}</p>
                            <p class="text-sm text-gray-500">{{ $request->product->sku }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $request->requested_quantity }} {{ $request->product->unit?->short_name ?? 'pcs' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($request->estimated_cost ?? 0, 0) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $request->status == 'processed' ? 'bg-green-100 text-green-700' : 
                                   ($request->status == 'approved' ? 'bg-blue-100 text-blue-700' : 
                                   ($request->status == 'rejected' ? 'bg-red-100 text-red-700' : 
                                   'bg-yellow-100 text-yellow-700')) }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $request->supplier->name ?? 'Not Assigned' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $request->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('storekeeper.purchase-order-requests.show', $request) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
