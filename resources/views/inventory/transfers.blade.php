@extends('layouts.app')

@section('page-title', 'Stock Transfers')

@section('scripts')
<script>
    function showDeleteModal(transferId) {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
        document.getElementById('deleteForm').action = '/inventory/transfers/' + transferId;
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
</script>
@endsection

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Stock Transfers</h2>
            <a href="{{ route('inventory.transfers.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>New Transfer
            </a>
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
                        <th class="text-left">Ref. Number</th>
                        <th class="text-left">Product</th>
                        <th class="text-left">From Location</th>
                        <th class="text-left">To Location</th>
                        <th class="text-left">Quantity</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $transfer)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('inventory.transfers.show', $transfer->id) }}" class="hover:underline">{{ $transfer->transfer_number }}</a>
                        </td>
                        <td class="text-gray-600">{{ $transfer->product->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ $transfer->fromLocation->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ $transfer->toLocation->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ $transfer->quantity }}</td>
                        <td class="text-gray-600">{{ $transfer->transfer_date ? date('M d, Y', strtotime($transfer->transfer_date)) : '-' }}</td>
                        <td>
                            <span class="badge badge-green">Completed</span>
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('inventory.transfers.show', $transfer->id) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('inventory.transfers.edit', $transfer->id) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="showDeleteModal({{ $transfer->id }})" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Delete Transfer</h3>
                <p class="text-gray-600">Are you sure you want to delete this transfer?</p>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="hideDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <form action="/inventory/transfers" method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection