@extends('layouts.app')

@section('page-title', 'Stock Requests')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Stock Requests</h1>
        <a href="{{ route('stock-requests.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>New Request
        </a>
    </div>

    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockRequests as $stockRequest)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-primary-900">{{ $stockRequest->request_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($stockRequest->request_type === 'online_order')
                                <span class="text-blue-600">Online Order</span>
                            @else
                                <span class="text-purple-600">Store Use</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stockRequest->status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($stockRequest->status === 'approved')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                            @elseif($stockRequest->status === 'rejected')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                            @elseif($stockRequest->status === 'completed')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Completed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $stockRequest->requested_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('stock-requests.show', $stockRequest) }}" class="text-primary-600 hover:text-primary-900 font-medium mr-3">
                                View Details
                            </a>
                            @if($stockRequest->status === 'approved' && (Auth::user()->role === 'storekeeper' || Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
                            <a href="{{ route('stock-requests.show', $stockRequest) }}" class="text-green-600 hover:text-green-900 font-medium">
                                Issue Products
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-boxes text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">No stock requests found</p>
                            <p class="text-sm">Get started by creating a new stock request.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($stockRequests->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $stockRequests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
