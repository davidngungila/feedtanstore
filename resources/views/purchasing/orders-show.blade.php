@extends('layouts.app')

@section('page-title', $purchaseOrder->po_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $purchaseOrder->po_number }}</h2>
            <div class="flex gap-3">
                @if($purchaseOrder->status !== 'received' && $purchaseOrder->sent_at)
                    <a href="{{ route('purchasing.grn.create') }}?purchase_order_id={{ $purchaseOrder->id }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-check-circle mr-2"></i>Receive Order
                    </a>
                @endif
                <a href="{{ route('purchasing.orders.download', $purchaseOrder) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <a href="{{ route('purchasing.orders.edit', $purchaseOrder) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('purchasing.orders') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Orders
                </a>
            </div>
        </div>

        @if($purchaseOrder->approval_status === 'pending')
        <div class="border-l-4 border-yellow-500 bg-yellow-50 p-6 rounded-2xl mb-6">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-clipboard-list text-2xl text-yellow-600"></i>
                <div>
                    <h3 class="text-lg font-bold text-yellow-900">Review Purchase Order</h3>
                    <p class="text-yellow-700">Edit any details if necessary, then approve or reject.</p>
                </div>
            </div>

            <form id="reviewForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                        <select name="supplier_id" id="supplier_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Date *</label>
                        <input type="date" name="order_date" id="order_date" value="{{ old('order_date', $purchaseOrder->order_date?->toDateString()) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expected Date</label>
                        <input type="date" name="expected_date" id="expected_date" value="{{ old('expected_date', $purchaseOrder->expected_date?->toDateString()) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                        <input type="number" step="0.01" name="discount" id="discount" value="{{ old('discount', $purchaseOrder->discount) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax</label>
                        <input type="number" step="0.01" name="tax" id="tax" value="{{ old('tax', $purchaseOrder->tax) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-primary-900">Products</h3>
                        <button type="button" id="addProduct" class="text-primary-600 hover:text-primary-800">
                            <i class="fas fa-plus mr-2"></i>Add Product
                        </button>
                    </div>
                    
                    <div id="productsContainer">
                        @foreach($purchaseOrder->items as $index => $item)
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 product-item">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                <select name="products[{{ $index }}][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product-select">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                            data-price="{{ $product->cost_price }}" 
                                            {{ (old('products.' . $index . '.product_id', $item->product_id) == $product->id) ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                                <input type="number" name="products[{{ $index }}][quantity]" 
                                       value="{{ old('products.' . $index . '.quantity', $item->quantity) }}" 
                                       min="1" required 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product-quantity">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                                <input type="number" step="0.01" name="products[{{ $index }}][unit_price]" 
                                       value="{{ old('products.' . $index . '.unit_price', $item->unit_price) }}" 
                                       required 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product-price">
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-product text-red-600 hover:text-red-800 px-4 py-2 border border-red-300 rounded-lg">
                                    <i class="fas fa-trash mr-2"></i>Remove
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @csrf
                <div class="flex justify-end gap-3">
                    <button type="submit" formaction="{{ route('purchasing.orders.review.reject', $purchaseOrder) }}" formmethod="POST" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Reject Order
                    </button>
                    <button type="submit" id="approveBtn" formaction="{{ route('purchasing.orders.review.approve', $purchaseOrder) }}" formmethod="POST" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Approve & Send to Supplier
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Supplier</p>
                <p class="font-medium">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Status</p>
                    <span class="badge {{ $purchaseOrder->status === 'received' ? 'badge-green' : ($purchaseOrder->status === 'canceled' ? 'badge-red' : 'badge-yellow') }}">
                        {{ ucfirst($purchaseOrder->status) }}
                    </span>
                </div>
                @if($purchaseOrder->sent_at)
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-600 text-2xl" title="Sent"></i>
                        <span class="text-sm text-gray-500">Sent to Supplier</span>
                    </div>
                @elseif($purchaseOrder->approval_status === 'approved')
                    <button id="sendToSupplierBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Send to Supplier
                    </button>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Approval Status</p>
                <span class="badge {{ $purchaseOrder->approval_status === 'approved' ? 'badge-green' : ($purchaseOrder->approval_status === 'rejected' ? 'badge-red' : 'badge-yellow') }}">
                    {{ ucfirst($purchaseOrder->approval_status) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Order Date</p>
                <p class="font-medium">{{ $purchaseOrder->order_date ? date('M d, Y', strtotime($purchaseOrder->order_date)) : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Expected Date</p>
                <p class="font-medium">{{ $purchaseOrder->expected_date ? date('M d, Y', strtotime($purchaseOrder->expected_date)) : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Subtotal</p>
                <p class="font-medium">TZS {{ number_format($purchaseOrder->subtotal, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Tax</p>
                <p class="font-medium">TZS {{ number_format($purchaseOrder->tax, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Discount</p>
                <p class="font-medium">TZS {{ number_format($purchaseOrder->discount, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Total</p>
                <p class="font-semibold text-lg">TZS {{ number_format($purchaseOrder->total, 2) }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Notes</p>
                <p>{{ $purchaseOrder->notes ?? '-' }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Products</h3>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="text-left">Quantity</th>
                        <th class="text-left">Unit Price</th>
                        <th class="text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $item)
                    <tr>
                        <td class="font-medium">{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ $item->quantity }}</td>
                        <td class="text-gray-600">TZS {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Progress Popup -->
    <div id="sendProgressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-primary-900 mb-4">Sending to Supplier</h3>
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span id="progressText">Preparing...</span>
                    <span id="progressPercent">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
            <div id="statusList" class="space-y-2 text-sm">
                <div id="status-prepare" class="flex items-center gap-2 text-gray-500">
                    <i class="fas fa-circle text-xs"></i>
                    <span>Preparing purchase order details...</span>
                </div>
                <div id="status-email" class="flex items-center gap-2 text-gray-500">
                    <i class="fas fa-circle text-xs"></i>
                    <span>Sending email to supplier...</span>
                </div>
                <div id="status-notifications" class="flex items-center gap-2 text-gray-500">
                    <i class="fas fa-circle text-xs"></i>
                    <span>Sending internal notifications...</span>
                </div>
                <div id="status-complete" class="flex items-center gap-2 text-gray-500">
                    <i class="fas fa-circle text-xs"></i>
                    <span>Completing...</span>
                </div>
            </div>
        </div>
    </div>

    {{-- @if($purchaseOrder->approval_status === 'pending')
    <script>
    let productIndex = {{ $purchaseOrder->items->count() }};

    document.getElementById('addProduct').addEventListener('click', function() {
        const container = document.getElementById('productsContainer');
        const template = document.querySelector('.product-item');
        const clone = template.cloneNode(true);
        
        clone.querySelectorAll('input, select').forEach(input => {
            const name = input.name.replace(/\[\d+\]/, '[' + productIndex + ']');
            input.name = name;
            input.value = input.tagName === 'SELECT' ? '' : (input.name.includes('quantity') ? '1' : '');
        });
        
        container.appendChild(clone);
        productIndex++;
        
        clone.querySelector('.remove-product').addEventListener('click', function() {
            if (document.querySelectorAll('.product-item').length > 1) {
                clone.remove();
            } else {
                alert('You must have at least one product.');
            }
        });
        
        clone.querySelector('.product-select').addEventListener('change', function() {
            const price = this.options[this.selectedIndex].dataset.price || 0;
            clone.querySelector('.product-price').value = price;
        });
    });

    document.querySelectorAll('.remove-product').forEach(btn => {
        btn.addEventListener('click', function() {
            if (document.querySelectorAll('.product-item').length > 1) {
                this.closest('.product-item').remove();
            } else {
                alert('You must have at least one product.');
            }
        });
    });

    document.querySelectorAll('.product-select').forEach(select => {
        select.addEventListener('change', function() {
            const price = this.options[this.selectedIndex].dataset.price || 0;
            this.closest('.product-item').querySelector('.product-price').value = price;
        });
    });
    </script>
    @endif --}}

    @if($purchaseOrder->approval_status === 'approved' && !$purchaseOrder->sent_at)
    <script>
    document.getElementById('sendToSupplierBtn').addEventListener('click', async function() {
        const modal = document.getElementById('sendProgressModal');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressPercent = document.getElementById('progressPercent');
        const statusList = ['status-prepare', 'status-email', 'status-notifications', 'status-complete'];
        
        modal.classList.remove('hidden');
        this.disabled = true;
        this.classList.add('opacity-50');
        
        const updateProgress = (step, percent, text) => {
            progressBar.style.width = percent + '%';
            progressPercent.textContent = percent + '%';
            progressText.textContent = text;
            
            statusList.forEach((id, index) => {
                const el = document.getElementById(id);
                if (index < step) {
                    el.classList.remove('text-gray-500');
                    el.classList.add('text-green-600');
                    el.querySelector('i').classList.remove('fa-circle');
                    el.querySelector('i').classList.add('fa-check-circle');
                } else if (index === step) {
                    el.classList.remove('text-gray-500');
                    el.classList.add('text-blue-600');
                }
            });
        };
        
        try {
            // Step 1: Prepare (0-25%)
            updateProgress(0, 10, 'Preparing purchase order details...');
            await new Promise(resolve => setTimeout(resolve, 500));
            updateProgress(0, 25, 'Purchase order prepared');
            
            // Step 2: Send email (25-50%)
            updateProgress(1, 30, 'Sending email to supplier...');
            await new Promise(resolve => setTimeout(resolve, 1000));
            updateProgress(1, 50, 'Email sent successfully');
            
            // Step 3: Send notifications (50-75%)
            updateProgress(2, 55, 'Sending internal notifications...');
            await new Promise(resolve => setTimeout(resolve, 800));
            updateProgress(2, 75, 'Notifications sent');
            
            // Step 4: Call backend (75-100%)
            updateProgress(3, 80, 'Updating system...');
            const response = await fetch("{{ route('purchasing.orders.send', $purchaseOrder) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to send purchase order');
            }
            
            updateProgress(3, 100, 'Complete!');
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Reload page to show updated status
            window.location.reload();
            
        } catch (error) {
            alert('Error sending purchase order: ' + error.message);
            modal.classList.add('hidden');
            this.disabled = false;
            this.classList.remove('opacity-50');
        }
    });
    </script>
    @endif
</div>
@endsection
