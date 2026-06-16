@extends('layouts.app')

@section('page-title', 'Verify Receipt')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4 {{ $isVerified ? 'bg-green-100' : 'bg-red-100' }}">
                <i class="fas {{ $isVerified ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600' }} text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-primary-900 mb-2">Receipt Verification</h1>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full {{ $isVerified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                <i class="fas {{ $isVerified ? 'fa-check' : 'fa-times' }}"></i>
                <span class="font-medium">{{ $isVerified ? 'VERIFIED' : 'NOT VERIFIED' }}</span>
            </div>
        </div>

        <div class="space-y-4 mb-8">
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="font-bold text-primary-900 mb-3">Order Details</h2>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-500">Invoice #</p>
                        <p class="font-medium">{{ $sale->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Date</p>
                        <p class="font-medium">{{ $sale->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Customer</p>
                        <p class="font-medium">{{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Cashier</p>
                        <p class="font-medium">{{ $sale->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <span class="font-medium {{ $sale->status === 'completed' ? 'text-green-600' : 'text-red-600' }}">{{ ucfirst($sale->status) }}</span>
                    </div>
                    <div>
                        <p class="text-gray-500">Payment Method</p>
                        <p class="font-medium">{{ ucfirst($sale->payment_method) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="font-bold text-primary-900 mb-3">Items</h2>
                <div class="space-y-3">
                    @foreach($sale->items as $item)
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <p class="font-medium">{{ $item->product->name ?? 'Product' }}</p>
                            <p class="text-gray-500">{{ $item->quantity }} x TZS {{ number_format($item->unit_price, 2) }}</p>
                        </div>
                        <p class="font-bold">TZS {{ number_format($item->total, 2) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="font-bold text-primary-900 mb-3">Totals</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">TZS {{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    @if($sale->discount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-medium text-red-600">- TZS {{ number_format($sale->discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-lg pt-2 border-t border-gray-200">
                        <span class="font-bold">Total</span>
                        <span class="font-bold">TZS {{ number_format($sale->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Paid</span>
                        <span class="font-medium">TZS {{ number_format($sale->paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Change</span>
                        <span class="font-medium">TZS {{ number_format($sale->change, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 justify-center">
            @auth
                <a href="{{ route('dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Go to Dashboard
                </a>
                <a href="{{ route('sales.receipts.print', $sale) }}" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Receipt
                </a>
            @else
                <a href="javascript:window.print()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print This Page
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection