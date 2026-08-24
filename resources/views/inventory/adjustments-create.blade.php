@extends('layouts.app')

@section('page-title', 'New Stock Adjustment')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">New Stock Adjustment</h2>
            <a href="{{ route('inventory.adjustments') }}" class="text-primary-600 hover:text-primary-800">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        @if($errors->any())
            <div class="mb-6 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('inventory.adjustments.store') }}" id="adjustment-form">
            @csrf

            <!-- Adjustment Items -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-semibold text-primary-900">Products</label>
                    <button type="button" id="add-item-btn" class="bg-primary-50 hover:bg-primary-100 text-primary-700 border border-primary-200 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-plus mr-1"></i>Add Product
                    </button>
                </div>

                <div class="hidden md:grid grid-cols-12 gap-3 px-1 mb-2">
                    <label class="col-span-6 text-xs font-medium text-gray-500 uppercase tracking-wide">Product</label>
                    <label class="col-span-2 text-xs font-medium text-gray-500 uppercase tracking-wide">Type</label>
                    <label class="col-span-3 text-xs font-medium text-gray-500 uppercase tracking-wide">Quantity Change</label>
                    <span class="col-span-1"></span>
                </div>

                <div id="items-container" class="space-y-3">
                    @php
                        $oldItems = old('items', [['product_id' => '', 'type' => 'addition', 'quantity_change' => '']]);
                    @endphp
                    @foreach($oldItems as $index => $oldItem)
                        <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-3 p-3 border border-gray-200 rounded-lg bg-gray-50/50">
                            <div class="md:col-span-6">
                                <label class="block text-xs font-medium text-gray-500 md:hidden mb-1">Product</label>
                                <select name="items[{{ $index }}][product_id]" class="item-product w-full p-3 rounded-lg border border-primary-200 bg-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('items.' . $index . '.product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (Stock: {{ $product->quantity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 md:hidden mb-1">Type</label>
                                <select name="items[{{ $index }}][type]" class="item-type w-full p-3 rounded-lg border border-primary-200 bg-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required>
                                    <option value="addition" {{ old('items.' . $index . '.type') === 'subtraction' ? '' : 'selected' }}>Addition (+)</option>
                                    <option value="subtraction" {{ old('items.' . $index . '.type') === 'subtraction' ? 'selected' : '' }}>Subtraction (-)</option>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 md:hidden mb-1">Quantity Change</label>
                                <input type="number" name="items[{{ $index }}][quantity_change]" min="1" value="{{ old('items.' . $index . '.quantity_change', '') }}" class="item-quantity w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required placeholder="Enter quantity">
                            </div>
                            <div class="md:col-span-1 flex md:justify-center items-center">
                                <button type="button" class="remove-item-btn w-10 h-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors {{ count($oldItems) <= 1 ? 'opacity-40 pointer-events-none' : '' }}" title="Remove product" {{ count($oldItems) <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Each product can only appear once per adjustment.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Adjustment Date</label>
                    <input type="date" name="adjustment_date" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required value="{{ old('adjustment_date', date('Y-m-d')) }}">
                    @error('adjustment_date')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Reason</label>
                    <input type="text" name="reason" value="{{ old('reason') }}" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required placeholder="Enter reason">
                    @error('reason')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" placeholder="Enter notes">{{ old('notes') }}</textarea>
                </div>

                <div class="col-span-1 md:col-span-2 flex gap-3 mt-2">
                    <button type="submit" id="submitBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Adjustment
                    </button>
                    <a href="{{ route('inventory.adjustments') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        var container = document.getElementById('items-container');
        var addBtn = document.getElementById('add-item-btn');

        function updateRemoveButtons() {
            var rows = container.querySelectorAll('.item-row');
            rows.forEach(function(row) {
                var btn = row.querySelector('.remove-item-btn');
                if (rows.length <= 1) {
                    btn.classList.add('opacity-40', 'pointer-events-none');
                    btn.disabled = true;
                } else {
                    btn.classList.remove('opacity-40', 'pointer-events-none');
                    btn.disabled = false;
                }
            });
        }

        function reindexRows() {
            container.querySelectorAll('.item-row').forEach(function(row, index) {
                row.querySelectorAll('select, input').forEach(function(field) {
                    field.name = field.name.replace(/items\[\d+\]/, 'items[' + index + ']');
                });
            });
        }

        addBtn.addEventListener('click', function() {
            var rows = container.querySelectorAll('.item-row');
            if (rows.length === 0) return;

            var newRow = rows[rows.length - 1].cloneNode(true);
            var newIndex = rows.length;

            newRow.querySelectorAll('select, input').forEach(function(field) {
                field.name = field.name.replace(/items\[\d+\]/, 'items[' + newIndex + ']');
                if (field.classList.contains('item-product')) {
                    field.value = '';
                } else if (field.classList.contains('item-type')) {
                    field.value = 'addition';
                } else if (field.classList.contains('item-quantity')) {
                    field.value = '';
                }
            });

            container.appendChild(newRow);
            updateRemoveButtons();
            newRow.querySelector('.item-product').focus();
        });

        container.addEventListener('click', function(e) {
            var btn = e.target.closest('.remove-item-btn');
            if (!btn || btn.disabled) return;
            btn.closest('.item-row').remove();
            reindexRows();
            updateRemoveButtons();
        });

        // Prevent duplicate product selections
        container.addEventListener('change', function(e) {
            if (!e.target.classList.contains('item-product')) return;
            var selected = e.target.value;
            if (!selected) return;

            container.querySelectorAll('.item-product').forEach(function(select) {
                if (select !== e.target && select.value === selected) {
                    e.target.value = '';
                    alert('This product is already added. Each product can only appear once.');
                }
            });
        });

        document.getElementById('adjustment-form').addEventListener('submit', function(e) {
            var submitBtn = document.getElementById('submitBtn');
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            submitBtn.classList.remove('bg-primary-600', 'hover:bg-primary-700');
            submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        });
    })();
</script>
@endsection
