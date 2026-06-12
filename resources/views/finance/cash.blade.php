@extends('layouts.app')

@section('page-title', 'Cash Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Cash Management</h2>
            <a href="{{ route('finance.cash.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Cash Register
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($cashRegisters as $register)
            <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold text-primary-900">{{ $register->name }}</h3>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                        @if($register->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                        {{ $register->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-1">Opening Balance</p>
                    <p class="text-xl font-bold text-gray-800">TZS {{ number_format($register->opening_balance, 2) }}</p>
                </div>
                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-1">Current Balance</p>
                    <p class="text-2xl font-bold text-green-700">TZS {{ number_format($register->current_balance, 2) }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="#" class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm hover:bg-blue-200 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Add Cash
                    </a>
                    <a href="#" class="px-3 py-1 bg-orange-100 text-orange-800 rounded text-sm hover:bg-orange-200 transition-colors">
                        <i class="fas fa-minus mr-1"></i> Remove Cash
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full p-12 text-center text-gray-500">
                No cash registers found.
            </div>
            @endforelse
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Active Shifts</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Cashier</th>
                        <th class="px-4 py-3 text-left text-gray-700">Start Time</th>
                        <th class="px-4 py-3 text-left text-gray-700">Opening Cash</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($activeShifts as $shift)
                    <tr>
                        <td class="px-4 py-3">{{ $shift->user->name }}</td>
                        <td class="px-4 py-3">{{ $shift->opened_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-semibold">TZS {{ number_format($shift->opening_cash, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                Open
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('sales.shifts.close', $shift) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded transition-colors text-sm">
                                    Close Shift
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No active shifts.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection