@extends('layouts.app')

@section('page-title', 'Open Cash Drawer')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-8 max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cash-register text-4xl text-primary-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-primary-900">Open Cash Drawer</h1>
            <p class="text-gray-600 mt-2">Enter your starting cash balance to begin your shift</p>
        </div>

            <form action="{{ route('cash-drawer-sessions.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cash Balance (TZS)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TZS</span>
                                <input type="number" name="cash_balance" class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg" required min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lipa Namba Voda (TZS)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TZS</span>
                                <input type="number" name="mobile_balance" class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg" required min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bank Balance (TZS)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TZS</span>
                                <input type="number" name="bank_balance" class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg" required min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ClickPesa Balance (TZS)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TZS</span>
                                <input type="number" name="online_balance" class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg" required min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" rows="2" placeholder="Any notes about starting balance..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-unlock mr-2"></i>Open Cash Drawer
                    </button>
                </div>
            </form>

            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-2"></i>
                    <p class="text-sm text-yellow-800">
                        You must open the cash drawer before starting sales. At the end of your shift, you'll need to close the drawer and perform reconciliation before logout.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
