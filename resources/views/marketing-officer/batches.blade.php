@extends('layouts.app')

@section('page-title', 'Dispatch Batches')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-primary-900">Dispatch Batches</h1>
            <p class="text-gray-600">Monitor bulk dispatch batches and rider acceptance</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing-officer.bulk-dispatch') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-layer-group mr-2"></i>New Bulk Dispatch
            </a>
            <a href="{{ route('marketing-officer.orders') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Orders
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Target Rider</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Accepted By</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($batches as $batch)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-6 py-4">
                            <a href="{{ route('marketing-officer.dispatch-batch-details', $batch->id) }}" class="font-semibold text-primary-600 hover:text-primary-800">#{{ $batch->id }}</a>
                            <p class="text-xs text-gray-500">{{ $batch->creator->name ?? 'Marketing Officer' }}</p>
                            @if($batch->notes)
                                <p class="text-xs text-gray-500 mt-1 italic">{{ $batch->notes }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $batch->requests->count() }} order(s)</p>
                            <p class="text-xs text-gray-500 max-w-[220px] truncate">
                                {{ $batch->requests->map(fn ($r) => $r->order->order_number ?? '#'.$r->online_order_id)->implode(', ') }}
                            </p>
                            <p class="text-xs text-gray-500">TZS {{ number_format($batch->requests->sum(fn ($r) => $r->order->total ?? 0), 0) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $batch->targetRider->name ?? 'All available' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $batch->status == 'accepted' ? 'bg-green-100 text-green-700' : 
                                   ($batch->status == 'cancelled' ? 'bg-red-100 text-red-700' : 
                                   'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($batch->acceptedRider)
                                <span class="text-green-700 font-medium">{{ $batch->acceptedRider->name }}</span>
                                <p class="text-xs text-gray-500">{{ $batch->accepted_at ? $batch->accepted_at->format('M d, H:i') : '' }}</p>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $batch->expires_at ? $batch->expires_at->format('M d, H:i') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $batch->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No dispatch batches yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $batches->links() }}
        </div>
    </div>
</div>
@endsection
