@extends('layouts.app')

@section('page-title', 'Sales Returns')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    @if(request('sale'))
        @php
            $sale = \App\Models\Sale::with('items.product')->find(request('sale'));
        @endphp
        <div class="card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-primary-900">Create Return for {{ $sale->invoice_number }}</h2>
                <a href="{{ route('sales.returns') }}" class="px-4 py-2 border border-gray-300 rounded-lg">Back</a>
            </div>
            
            <form action="{{ route('sales.returns.store') }}" method="POST">
                @csrf
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                
                <div class="mb-6">
                    <h3 class="font-semibold text-primary-800 mb-3">Select Items to Return</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-2">Product</th>
                                    <th class="text-left py-2">Price</th>
                                    <th class="text-left py-2">Sold Qty</th>
                                    <th class="text-left py-2">Return Qty</th>
                                    <th class="text-left py-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-3">{{ $item->product->name ?? 'Product' }}</td>
                                        <td class="py-3">TZS {{ number_format($item->unit_price, 2) }}</td>
                                        <td class="py-3">{{ $item->quantity }}</td>
                                        <td class="py-3">
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" 
                                                       name="items[{{ $loop->index }}][sale_item_id]" 
                                                       value="{{ $item->id }}" 
                                                       class="return-checkbox">
                                                <input type="number" 
                                                       name="items[{{ $loop->index }}][quantity]" 
                                                       min="1" 
                                                       max="{{ $item->quantity }}" 
                                                       value="1" 
                                                       disabled 
                                                       class="return-qty w-20 px-3 py-2 border border-gray-300 rounded text-center">
                                            </div>
                                        </td>
                                        <td class="py-3 return-item-total">TZS 0.00</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Return</label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                
                <div class="flex flex-col items-end mb-6">
                    <div class="text-2xl font-bold text-primary-900">
                        Total Return: TZS <span id="return-total">0.00</span>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2">
                    <a href="{{ route('sales.returns') }}" class="px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Process Return</button>
                </div>
            </form>
        </div>
    @endif

    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Sales Returns</h2>
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
                        <th class="text-left">Return #</th>
                        <th class="text-left">Invoice</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returns as $return)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $return->return_number }}</td>
                        <td class="text-gray-600">{{ $return->sale->invoice_number }}</td>
                        <td class="text-gray-600">{{ $return->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-gray-600">TZS {{ number_format($return->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.return-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const qtyInput = this.closest('td').querySelector('.return-qty');
            const itemTotalEl = this.closest('tr').querySelector('.return-item-total');
            
            if (this.checked) {
                qtyInput.disabled = false;
                updateItemTotal(qtyInput, itemTotalEl);
            } else {
                qtyInput.disabled = true;
                qtyInput.value = 1;
                itemTotalEl.textContent = 'TZS 0.00';
            }
            
            updateTotal();
        });
        
        const qtyInput = checkbox.closest('td').querySelector('.return-qty');
        qtyInput.addEventListener('input', function() {
            const itemTotalEl = this.closest('tr').querySelector('.return-item-total');
            updateItemTotal(this, itemTotalEl);
            updateTotal();
        });
    });
    
    function updateItemTotal(qtyInput, itemTotalEl) {
        const qty = parseInt(qtyInput.value) || 0;
        const tr = qtyInput.closest('tr');
        const priceEl = tr.querySelectorAll('td')[1];
        const priceText = priceEl.textContent.replace('TZS ', '').replace(/,/g, '');
        const price = parseFloat(priceText);
        const total = qty * price;
        itemTotalEl.textContent = 'TZS ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.return-item-total').forEach(el => {
            const text = el.textContent.replace('TZS ', '').replace(/,/g, '');
            total += parseFloat(text) || 0;
        });
        document.getElementById('return-total').textContent = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
});
</script>
@endsection