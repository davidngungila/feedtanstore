@extends('layouts.app')

@section('page-title', 'Edit Credit Sale')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Credit Sale</h2>
            <a href="{{ route('sales.credit') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        <form id="creditSaleForm" action="{{ route('sales.credit.update', $sale) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                <select name="customer_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ $customer->id == $sale->customer_id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Discount</label>
                <select name="discount_id" id="discountSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">No Discount</option>
                    @foreach($discounts as $discount)
                    <option value="{{ $discount->id }}" {{ $discount->id == $sale->discount_id ? 'selected' : '' }} data-type="{{ $discount->type }}" data-value="{{ $discount->value }}">{{ $discount->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Items</label>
                <div id="editCartItems">
                    @foreach($sale->items as $item)
                    <div class="flex items-center gap-4 mb-3 p-3 bg-primary-50 rounded-lg edit-cart-item">
                        <input type="hidden" name="items[{{ $item->id }}][product_id]" value="{{ $item->product_id }}">
                        <div class="flex-1">
                            <p class="font-medium text-primary-900">{{ $item->product->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600">TZS {{ number_format($item->unit_price, 2) }} each</p>
                        </div>
                        <input type="number" name="items[{{ $item->id }}][quantity]" value="{{ $item->quantity }}" min="1" class="w-20 px-2 py-1 border border-gray-300 rounded edit-quantity" data-price="{{ $item->unit_price }}">
                        <input type="hidden" name="items[{{ $item->id }}][unit_price]" value="{{ $item->unit_price }}">
                        <span class="edit-item-total font-medium w-28 text-right">TZS {{ number_format($item->total, 2) }}</span>
                        <button type="button" onclick="removeEditItem(this)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add New Product</label>
                    <select id="addProductSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Select a product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->selling_price }}">{{ $product->name }} (TZS {{ number_format($product->selling_price, 2) }})</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="addEditItem()" class="mt-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Add Product</button>
                </div>
            </div>

            <div class="border-t pt-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal:</span>
                    <span id="editSubtotal" class="font-semibold">TZS {{ number_format($sale->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Discount:</span>
                    <span id="editDiscountAmount" class="font-semibold text-red-600">-TZS {{ number_format($sale->discount, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2 text-lg font-bold">
                    <span>Total:</span>
                    <span id="editTotal">TZS {{ number_format($sale->total, 2) }}</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="cash" {{ $sale->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ $sale->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                    <option value="mobile" {{ $sale->payment_method == 'mobile' ? 'selected' : '' }}>Mobile Money</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Paid Amount</label>
                <input type="number" name="paid" value="{{ $sale->paid }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">{{ $sale->notes }}</textarea>
            </div>

            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </form>
    </div>
</div>

<script>
let editItemIndex = {{ $sale->items->max('id') + 1 }};

function calculateEditTotals() {
    let subtotal = 0;
    const items = document.querySelectorAll('.edit-cart-item');
    items.forEach(item => {
        const quantity = parseFloat(item.querySelector('.edit-quantity').value) || 0;
        const price = parseFloat(item.querySelector('.edit-quantity').dataset.price) || 0;
        const total = quantity * price;
        subtotal += total;
        item.querySelector('.edit-item-total').textContent = 'TZS ' + total.toFixed(2);
    });
    
    const discountSelect = document.getElementById('discountSelect');
    let discount = 0;
    if (discountSelect.value) {
        const selectedOption = discountSelect.options[discountSelect.selectedIndex];
        const type = selectedOption.dataset.type;
        const value = parseFloat(selectedOption.dataset.value);
        if (type === 'percentage') {
            discount = subtotal * (value / 100);
        } else {
            discount = value;
        }
    }
    
    const total = subtotal - discount;
    
    document.getElementById('editSubtotal').textContent = 'TZS ' + subtotal.toFixed(2);
    document.getElementById('editDiscountAmount').textContent = '-TZS ' + discount.toFixed(2);
    document.getElementById('editTotal').textContent = 'TZS ' + total.toFixed(2);
}

function removeEditItem(button) {
    button.closest('.edit-cart-item').remove();
    calculateEditTotals();
}

function addEditItem() {
    const select = document.getElementById('addProductSelect');
    if (!select.value) return;
    
    const selectedOption = select.options[select.selectedIndex];
    const productId = select.value;
    const productName = selectedOption.dataset.name;
    const productPrice = parseFloat(selectedOption.dataset.price);
    
    const cartHtml = `
        <div class="flex items-center gap-4 mb-3 p-3 bg-primary-50 rounded-lg edit-cart-item">
            <input type="hidden" name="items[new${editItemIndex}][product_id]" value="${productId}">
            <div class="flex-1">
                <p class="font-medium text-primary-900">${productName}</p>
                <p class="text-sm text-gray-600">TZS ${productPrice.toFixed(2)} each</p>
            </div>
            <input type="number" name="items[new${editItemIndex}][quantity]" value="1" min="1" class="w-20 px-2 py-1 border border-gray-300 rounded edit-quantity" data-price="${productPrice}" onchange="calculateEditTotals()">
            <input type="hidden" name="items[new${editItemIndex}][unit_price]" value="${productPrice}">
            <span class="edit-item-total font-medium w-28 text-right">TZS ${productPrice.toFixed(2)}</span>
            <button type="button" onclick="removeEditItem(this)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    document.getElementById('editCartItems').insertAdjacentHTML('beforeend', cartHtml);
    editItemIndex++;
    select.value = '';
    calculateEditTotals();
}

document.getElementById('discountSelect').addEventListener('change', calculateEditTotals);
document.querySelectorAll('.edit-quantity').forEach(input => {
    input.addEventListener('change', calculateEditTotals);
});
</script>
@endsection
