@extends('layouts.app')

@section('page-title', 'New Supplier Payment')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Record Supplier Payment</h2>
            <a href="{{ route('purchasing.payments') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Payments
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('purchasing.payments.store') }}" method="POST">
            @csrf
            <!-- Hidden supplier id, will be set by JS -->
            <input type="hidden" name="supplier_id" id="supplierIdInput" value="{{ old('supplier_id') ?? ($selectedPO->supplier_id ?? '') }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order *</label>
                    <select name="purchase_order_id" id="purchaseOrderSelect" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Purchase Order</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <input type="text" id="supplierNameInput" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                    <input type="number" step="0.01" name="amount" id="amountInput" value="{{ old('amount') ?? ($selectedPO->total ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                    <select name="payment_method" id="paymentMethod" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" onchange="toggleTransactionId()">
                        <option value="">Select Method</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                    </select>
                </div>
                <div class="md:col-span-2" id="transactionIdContainer" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID *</label>
                    <input type="text" name="transaction_id" id="transactionId" value="{{ old('transaction_id') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('purchasing.payments') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Record Payment
                </button>
            </div>
        </form>

        <script>
            // Pass purchase orders data to JS
            const purchaseOrders = @json($purchaseOrdersData);

            function toggleTransactionId() {
                const paymentMethod = document.getElementById('paymentMethod').value;
                const transactionIdContainer = document.getElementById('transactionIdContainer');
                const transactionIdInput = document.getElementById('transactionId');
                
                if (paymentMethod === 'card' || paymentMethod === 'bank_transfer' || paymentMethod === 'mobile_money') {
                    transactionIdContainer.style.display = 'block';
                    transactionIdInput.required = true;
                } else {
                    transactionIdContainer.style.display = 'none';
                    transactionIdInput.required = false;
                }
            }

            function updatePOSelectOptions() {
                const poSelect = document.getElementById('purchaseOrderSelect');
                
                // Clear existing options except first one
                poSelect.innerHTML = '<option value="">Select Purchase Order</option>';
                
                // Add all POs
                purchaseOrders.forEach(po => {
                    const option = document.createElement('option');
                    option.value = po.id;
                    option.textContent = po.po_number + ' - TZS ' + parseFloat(po.total).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    option.dataset.total = po.total;
                    option.dataset.supplier_id = po.supplier_id;
                    option.dataset.supplier_name = po.supplier_name;
                    poSelect.appendChild(option);
                });
            }

            function updateFormFromPO() {
                const poSelect = document.getElementById('purchaseOrderSelect');
                const supplierIdInput = document.getElementById('supplierIdInput');
                const supplierNameInput = document.getElementById('supplierNameInput');
                const amountInput = document.getElementById('amountInput');
                
                if (poSelect.value) {
                    const selectedOption = poSelect.options[poSelect.selectedIndex];
                    supplierIdInput.value = selectedOption.dataset.supplier_id;
                    supplierNameInput.value = selectedOption.dataset.supplier_name;
                    amountInput.value = parseFloat(selectedOption.dataset.total).toFixed(2);
                } else {
                    supplierIdInput.value = '';
                    supplierNameInput.value = '';
                    amountInput.value = '';
                }
            }
            
            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                toggleTransactionId();
                updatePOSelectOptions();
                
                // Set initial PO if old value exists or selectedPO is present
                const oldPO = @json(old('purchase_order_id'));
                const selectedPO = @json($selectedPO->id ?? null);
                const initialPO = oldPO || selectedPO;
                if (initialPO) {
                    document.getElementById('purchaseOrderSelect').value = initialPO;
                    updateFormFromPO();
                }
                
                // Add event listener
                document.getElementById('purchaseOrderSelect').addEventListener('change', updateFormFromPO);
            });
        </script>
    </div>
</div>
@endsection
