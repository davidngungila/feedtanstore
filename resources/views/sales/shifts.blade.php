@extends('layouts.app')

@section('page-title', 'Shifts')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Shifts</h2>
            
            @if($currentShift)
            <form action="{{ route('sales.shifts.close', $currentShift) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this shift?')">
                @csrf
                <button type="button" data-modal-target="closeShiftModal" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-lock mr-2"></i>Close Shift
                </button>
            </form>
            @else
            <button type="button" data-modal-target="openShiftModal" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-lock-open mr-2"></i>Open Shift
            </button>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($currentShift)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
            <h3 class="font-bold text-blue-800 mb-2">Current Shift</h3>
            <p class="text-sm text-blue-700">
                <strong>Opened:</strong> {{ $currentShift->opened_at->format('M d, Y H:i') }} by {{ $currentShift->user->name }}
            </p>
            <p class="text-sm text-blue-700"><strong>Opening Cash:</strong> TZS {{ number_format($currentShift->opening_cash, 2) }}</p>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">User</th>
                        <th class="text-left">Opened</th>
                        <th class="text-left">Closed</th>
                        <th class="text-left">Opening Cash</th>
                        <th class="text-left">Cash Sales</th>
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shifts as $shift)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $shift->user->name }}</td>
                        <td class="text-gray-600">{{ $shift->opened_at->format('M d, Y H:i') }}</td>
                        <td class="text-gray-600">{{ $shift->closed_at ? $shift->closed_at->format('M d, Y H:i') : '-' }}</td>
                        <td class="text-gray-600">TZS {{ number_format($shift->opening_cash, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($shift->cash_sales, 2) }}</td>
                        <td>
                            <span class="badge {{ $shift->closed_at ? 'badge-gray' : 'badge-green' }}">
                                {{ $shift->closed_at ? 'Closed' : 'Open' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Open Shift Modal -->
<div id="openShiftModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-primary-900 mb-4">Open Shift</h3>
        <form action="{{ route('sales.shifts.open') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Opening Cash</label>
                <input type="number" name="opening_cash" value="0" min="0" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" data-modal-close="openShiftModal" class="px-4 py-2 border border-gray-300 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg">Open Shift</button>
            </div>
        </form>
    </div>
</div>

@if($currentShift)
<!-- Close Shift Modal -->
<div id="closeShiftModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-primary-900 mb-4">Close Shift</h3>
        <form action="{{ route('sales.shifts.close', $currentShift) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Closing Cash</label>
                <input type="number" name="closing_cash" value="0" min="0" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" data-modal-close="closeShiftModal" class="px-4 py-2 border border-gray-300 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Close Shift</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
document.addEventListener('click', (e) => {
    if (e.target.dataset.modalTarget) {
        document.getElementById(e.target.dataset.modalTarget).classList.remove('hidden');
    }
    if (e.target.dataset.modalClose) {
        document.getElementById(e.target.dataset.modalClose).classList.add('hidden');
    }
});
</script>
@endsection
