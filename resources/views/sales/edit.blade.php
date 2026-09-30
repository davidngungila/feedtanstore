@extends('layouts.app')

@section('page-title', 'Edit Sale ' . $sale->invoice_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
        <h2 class="text-xl font-bold text-primary-900">Edit Sale — {{ $sale->invoice_number }}</h2>
        <a href="{{ route('sales.show', $sale) }}" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
            <i class="fas fa-arrow-left mr-1.5"></i>Back to Sale
        </a>
    </div>

    @if($sale->tra_status == 'posted')
        <div class="mb-4 p-3 bg-amber-50 border border-amber-300 text-amber-800 rounded-lg">
            <p class="text-sm"><i class="fas fa-circle-info mr-2"></i>This sale was already fiscalised at TRA (receipt <span class="font-semibold">{{ $sale->tra_receipt_number ?: $sale->invoice_number }}</span>). You can edit it in our system, but the TRA receipt is <strong>not</strong> re-posted &mdash; only one fiscal receipt is issued per sale.</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sold products only -->
        <div class="lg:col-span-2">
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-xl font-bold text-primary-900">Products in this sale</h2>
                    <span class="text-sm text-gray-500">{{ $sale->invoice_number }} • {{ $sale->items->sum('quantity') }} item(s)</span>
                </div>
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800"><i class="fas fa-info-circle mr-2"></i>Only the products sold in this sale are listed here. Adjust quantity or price, or remove a line — stock, totals, customer balance and shift figures update automatically on save.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b text-left text-sm text-gray-500">
                                <th class="py-2 pr-2">Product</th>
                                <th class="py-2 pr-2 text-right">Price (TZS)</th>
                                <th class="py-2 pr-2 text-right">Qty</th>
                                <th class="py-2 pr-2 text-right">Line total</th>
                                <th class="py-2 text-right">Remove</th>
                            </tr>
                        </thead>
                        <tbody id="soldItemsBody"></tbody>
                    </table>
                    <p id="soldItemsEmpty" class="hidden text-sm text-gray-500 py-6 text-center">No products left in this sale. A sale must contain at least one product to be saved.</p>
                </div>
            </div>
        </div>

        <!-- Cart & Payment -->
        <div class="lg:col-span-1">
            <div class="card rounded-2xl p-6 sticky top-6">
                <h2 class="text-xl font-bold text-primary-900 mb-4">Sale details</h2>

                <form id="saleEditForm" action="{{ route('sales.update', $sale) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div id="cartHiddenInputs"></div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <select name="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (string) old('customer_id', $sale->customer_id) === (string) $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                        <select name="discount_id" id="discountSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="handleDiscountChange()">
                            <option value="">No Discount</option>
                            @foreach($discounts as $discount)
                            <option value="{{ $discount->id }}" data-type="{{ $discount->type }}" data-value="{{ $discount->value }}" data-min="{{ $discount->min_amount }}" data-max="{{ $discount->max_amount }}" {{ (string) old('discount_id', $sale->discount_id) === (string) $discount->id ? 'selected' : '' }}>
                                {{ $discount->name }} ({{ $discount->type == 'percentage' ? $discount->value . '%' : 'TZS ' . number_format($discount->value, 2) }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <p class="text-gray-700 font-medium mb-2 text-sm">Payment Method</p>
                        <input type="hidden" name="payment_method" id="paymentMethod" value="{{ old('payment_method', $sale->payment_method) }}">
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" class="flex-1 py-2.5 border-2 rounded-lg font-semibold text-sm {{ old('payment_method', $sale->payment_method) == 'cash' ? 'border-primary-600 bg-primary-600 text-white' : 'border-gray-300 text-gray-700 hover:border-primary-500' }}" id="methodCash" onclick="selectPaymentMethod('cash')">
                                <i class="fas fa-money-bill mr-1"></i>Cash
                            </button>
                            <button type="button" class="flex-1 py-2.5 border-2 rounded-lg font-semibold text-sm {{ old('payment_method', $sale->payment_method) == 'card' ? 'border-primary-600 bg-primary-600 text-white' : 'border-gray-300 text-gray-700 hover:border-primary-500' }}" id="methodCard" onclick="selectPaymentMethod('card')">
                                <i class="fas fa-credit-card mr-1"></i>Card
                            </button>
                            <button type="button" class="flex-1 py-2.5 border-2 rounded-lg font-semibold text-sm {{ old('payment_method', $sale->payment_method) == 'clickpesa' ? 'border-primary-600 bg-primary-600 text-white' : 'border-gray-300 text-gray-700 hover:border-primary-500' }}" id="methodClickpesa" onclick="selectPaymentMethod('clickpesa')">
                                <i class="fas fa-mobile-alt mr-1"></i>ClickPesa
                            </button>
                            <button type="button" class="flex-1 py-2.5 border-2 rounded-lg font-semibold text-sm {{ old('payment_method', $sale->payment_method) == 'lipa_namba' ? 'border-primary-600 bg-primary-600 text-white' : 'border-gray-300 text-gray-700 hover:border-primary-500' }}" id="methodLipa_namba" onclick="selectPaymentMethod('lipa_namba')">
                                <i class="fas fa-store mr-1"></i>Lipa Namba
                            </button>
                        </div>
                    </div>

                    <div class="border-t pt-4 mb-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Subtotal:</span>
                            <span id="subtotal" class="font-semibold">TZS 0.00</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Discount:</span>
                            <span id="discountAmount" class="font-semibold text-red-600">-TZS 0.00</span>
                        </div>
                        <div class="flex justify-between mb-2 text-lg font-bold">
                            <span>Total:</span>
                            <span id="total">TZS 0.00</span>
                        </div>
                        <div class="flex justify-between mb-2 items-center">
                            <span class="text-gray-600">Paid Amount:</span>
                            <input type="number" name="paid" id="paidAmount" value="{{ old('paid', $sale->paid) }}" min="0" step="0.01" class="w-32 px-2 py-1 border border-gray-300 rounded text-right font-semibold" onchange="updateTotals();">
                        </div>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Change:</span>
                            <span id="change" class="text-green-600">TZS 0.00</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Optional notes...">{{ old('notes', $sale->notes) }}</textarea>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('sales.show', $sale) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
                        <button type="submit" id="updateSaleBtn" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Update Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let cart = @json($cartItems);
let currentDiscount = null;

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
    updateTotals();
}

function updateQuantity(index, quantity) {
    cart[index].quantity = Math.max(1, parseInt(quantity) || 1);
    renderCart();
    updateTotals();
}

function updateUnitPrice(index, price) {
    cart[index].unit_price = Math.max(0, parseFloat(price) || 0);
    renderCart();
    updateTotals();
}

function renderCart() {
    const body = document.getElementById('soldItemsBody');
    const empty = document.getElementById('soldItemsEmpty');
    const hidden = document.getElementById('cartHiddenInputs');
    if (cart.length === 0) {
        body.innerHTML = '';
        hidden.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }
    empty.classList.add('hidden');
    hidden.innerHTML = cart.map((item, index) => `
        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
        <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">`
    ).join('');
    body.innerHTML = cart.map((item, index) => {
        const line = item.quantity * item.unit_price;
        return `
        <tr class="border-b border-gray-100">
            <td class="py-3 pr-2">
                <p class="font-medium text-primary-900">${item.name}</p>
            </td>
            <td class="py-3 pr-2 text-right">
                <input type="number" value="${item.unit_price}" min="0" step="0.01"
                    onchange="updateUnitPrice(${index}, this.value)"
                    class="w-28 px-2 py-1 border border-gray-300 rounded text-right text-sm">
            </td>
            <td class="py-3 pr-2 text-right">
                <input type="number" value="${item.quantity}" min="1"
                    onchange="updateQuantity(${index}, this.value)"
                    class="w-20 px-2 py-1 border border-gray-300 rounded text-center text-sm">
            </td>
            <td class="py-3 pr-2 text-right font-medium">TZS ${line.toFixed(2)}</td>
            <td class="py-3 text-right">
                <button type="button" onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700" title="Remove line">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

function selectPaymentMethod(method) {
    document.getElementById('paymentMethod').value = method;
    document.querySelectorAll('[id^="method"]').forEach(btn => {
        btn.classList.remove('border-primary-600', 'bg-primary-600', 'text-white');
        btn.classList.add('border-gray-300', 'text-gray-700');
    });
    const active = document.getElementById('method' + method.charAt(0).toUpperCase() + method.slice(1));
    if (active) {
        active.classList.remove('border-gray-300', 'text-gray-700');
        active.classList.add('border-primary-600', 'bg-primary-600', 'text-white');
    }
    updateTotals();
}

function handleDiscountChange() {
    const select = document.getElementById('discountSelect');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        currentDiscount = {
            type: selectedOption.dataset.type,
            value: parseFloat(selectedOption.dataset.value),
            minAmount: selectedOption.dataset.min ? parseFloat(selectedOption.dataset.min) : null,
            maxAmount: selectedOption.dataset.max ? parseFloat(selectedOption.dataset.max) : null
        };
    } else {
        currentDiscount = null;
    }
    updateTotals();
}

function calculateDiscount(subtotal) {
    if (!currentDiscount) return 0;
    if ((currentDiscount.minAmount && subtotal < currentDiscount.minAmount) ||
        (currentDiscount.maxAmount && subtotal > currentDiscount.maxAmount)) {
        return 0;
    }
    if (currentDiscount.type === 'percentage') {
        return subtotal * (currentDiscount.value / 100);
    }
    return currentDiscount.value;
}

function updateTotals() {
    let subtotal = 0;
    cart.forEach(item => { subtotal += item.quantity * item.unit_price; });
    const discount = calculateDiscount(subtotal);
    const total = subtotal - discount;
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const change = paid - total;

    document.getElementById('subtotal').textContent = 'TZS ' + subtotal.toFixed(2);
    document.getElementById('discountAmount').textContent = '-TZS ' + discount.toFixed(2);
    document.getElementById('total').textContent = 'TZS ' + total.toFixed(2);
    const changeEl = document.getElementById('change');
    changeEl.textContent = 'TZS ' + change.toFixed(2);
    changeEl.classList.toggle('text-red-600', change < 0);
    changeEl.classList.toggle('text-green-600', change >= 0);
}

// init discount from preselected option
handleDiscountChange();
renderCart();
updateTotals();

document.getElementById('saleEditForm').addEventListener('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('This sale has no products left. A sale must contain at least one product.');
        return;
    }
    renderCart(); // refresh hidden inputs with latest qty/price
});
</script>
@endsection
