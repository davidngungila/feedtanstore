@extends('layouts.app')

@section('page-title', 'Close Cash Drawer')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-8 max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cash-register text-4xl text-red-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-primary-900">Close Cash Drawer</h1>
            <p class="text-gray-600 mt-2">Enter your ending cash balance to close your shift</p>
        </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Opening Cash Balance:</span>
                    <span class="font-semibold">TZS {{ number_format($session->cash_balance, 0) }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Opening Mobile Balance:</span>
                    <span class="font-semibold">TZS {{ number_format($session->mobile_balance, 0) }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Opening Bank Balance:</span>
                    <span class="font-semibold">TZS {{ number_format($session->bank_balance, 0) }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Opening Online Balance:</span>
                    <span class="font-semibold">TZS {{ number_format($session->online_balance, 0) }}</span>
                </div>
                <div class="flex justify-between items-center border-t pt-2 mt-2">
                    <span class="text-sm font-semibold text-gray-700">Total Opening Balance:</span>
                    <span class="font-bold text-primary-900">TZS {{ number_format($session->opening_balance, 0) }}</span>
                </div>
            </div>

            <form action="{{ route('cash-drawer-sessions.close', $session) }}" method="POST">
                @method('PUT')
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Closing Cash Balance (TZS)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TZS</span>
                            <input type="number" name="closing_cash_balance" class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg" required min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Closing Mobile Balance (TZS)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TZS</span>
                            <input type="number" name="closing_mobile_balance" class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg" required min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Closing Bank Balance (TZS)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TZS</span>
                            <input type="number" name="closing_bank_balance" class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg" required min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Closing Online Balance (TZS)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TZS</span>
                            <input type="number" name="closing_online_balance" class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg" required min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" rows="2" placeholder="Any notes about closing balance..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-lock mr-2"></i>Close Cash Drawer
                    </button>
                </div>
            </form>

            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
                    <p class="text-sm text-yellow-800">
                        After closing, you must wait for manager reconciliation before you can logout.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
