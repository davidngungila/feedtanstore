@extends('layouts.app')

@section('page-title', 'New Journal Entry')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">New Journal Entry</h2>
            <a href="{{ route('finance.journal-entries') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg border border-gray-300 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('finance.journal-entries.store') }}" method="POST" id="journalEntryForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="form-label">Entry Date</label>
                    <input type="date" name="entry_date" required value="{{ old('entry_date', date('Y-m-d')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <input type="text" name="description" required class="form-input" placeholder="Enter journal entry description">
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-900">Journal Items</h3>
                    <button type="button" id="addItemBtn" class="text-primary-600 hover:text-primary-800 font-medium">
                        <i class="fas fa-plus mr-2"></i>Add Item
                    </button>
                </div>

                <div id="itemsContainer">
                    <div class="item-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 items-end">
                        <div>
                            <label class="form-label">Account</label>
                            <select name="items[0][account_id]" required class="form-input">
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('items.0.account_id') == $account->id ? 'selected' : '' }}>{{ $account->account_code }} - {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Type</label>
                            <select name="items[0][type]" required class="form-input">
                                <option value="debit" {{ old('items.0.type') == 'debit' ? 'selected' : '' }}>Debit</option>
                                <option value="credit" {{ old('items.0.type') == 'credit' ? 'selected' : '' }}>Credit</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Amount</label>
                            <input type="number" name="items[0][amount]" required step="0.01" min="0.01" class="form-input amount-input" placeholder="0.00">
                        </div>
                        <div>
                            <label class="form-label">Description (optional)</label>
                            <input type="text" name="items[0][description]" class="form-input" placeholder="Item description">
                        </div>
                        <div></div>
                    </div>
                    <div class="item-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 items-end">
                        <div>
                            <label class="form-label">Account</label>
                            <select name="items[1][account_id]" required class="form-input">
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('items.1.account_id') == $account->id ? 'selected' : '' }}>{{ $account->account_code }} - {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Type</label>
                            <select name="items[1][type]" required class="form-input">
                                <option value="debit" {{ old('items.1.type') == 'debit' ? 'selected' : '' }}>Debit</option>
                                <option value="credit" {{ old('items.1.type') == 'credit' ? 'selected' : '' }}>Credit</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Amount</label>
                            <input type="number" name="items[1][amount]" required step="0.01" min="0.01" class="form-input amount-input" placeholder="0.00">
                        </div>
                        <div>
                            <label class="form-label">Description (optional)</label>
                            <input type="text" name="items[1][description]" class="form-input" placeholder="Item description">
                        </div>
                        <div></div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <span class="text-gray-600 font-medium">Total Debits: </span>
                        <span id="totalDebits" class="font-bold text-primary-900">0.00</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Total Credits: </span>
                        <span id="totalCredits" class="font-bold text-primary-900">0.00</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Balance: </span>
                        <span id="balance" class="font-bold text-primary-900">0.00</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Create Journal Entry
                </button>
                <a href="{{ route('finance.journal-entries') }}" class="text-gray-600 hover:text-gray-800 px-6 py-2 rounded-lg border border-gray-300 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    let itemCount = 2;
    const accounts = @json($accounts->map(function ($acc) { return ['id' => $acc->id, 'name' => $acc->account_code . ' - ' . $acc->name]; }));
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');

    function updateTotals() {
        let totalDebits = 0;
        let totalCredits = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const typeSelect = row.querySelector('select[name*="[type]"]');
            const amountInput = row.querySelector('.amount-input');
            const amount = parseFloat(amountInput.value) || 0;
            if (typeSelect.value === 'debit') {
                totalDebits += amount;
            } else {
                totalCredits += amount;
            }
        });
        document.getElementById('totalDebits').textContent = totalDebits.toFixed(2);
        document.getElementById('totalCredits').textContent = totalCredits.toFixed(2);
        document.getElementById('balance').textContent = (totalDebits - totalCredits).toFixed(2);
        const balanceSpan = document.getElementById('balance');
        if (Math.abs(totalDebits - totalCredits) < 0.01) {
            balanceSpan.classList.remove('text-red-600');
            balanceSpan.classList.add('text-green-600');
        } else {
            balanceSpan.classList.add('text-red-600');
            balanceSpan.classList.remove('text-green-600');
        }
    }

    document.querySelectorAll('.amount-input, select[name*="[type]"]').forEach(el => {
        el.addEventListener('input', updateTotals);
        el.addEventListener('change', updateTotals);
    });

    addItemBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'item-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 items-end';
        row.innerHTML = `
            <div>
                <label class="form-label">Account</label>
                <select name="items[${itemCount}][account_id]" required class="form-input">
                    <option value="">Select Account</option>
                    ${accounts.map(acc => `<option value="${acc.id}">${acc.name}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="form-label">Type</label>
                <select name="items[${itemCount}][type]" required class="form-input">
                    <option value="debit">Debit</option>
                    <option value="credit">Credit</option>
                </select>
            </div>
            <div>
                <label class="form-label">Amount</label>
                <input type="number" name="items[${itemCount}][amount]" required step="0.01" min="0.01" class="form-input amount-input" placeholder="0.00">
            </div>
            <div>
                <label class="form-label">Description (optional)</label>
                <input type="text" name="items[${itemCount}][description]" class="form-input" placeholder="Item description">
            </div>
            <div>
                <button type="button" class="remove-item text-red-600 hover:text-red-800 p-2">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        row.querySelector('.amount-input').addEventListener('input', updateTotals);
        row.querySelector('select[name*="[type]"]').addEventListener('change', updateTotals);
        row.querySelector('.remove-item').addEventListener('click', () => {
            row.remove();
            updateTotals();
        });
        itemsContainer.appendChild(row);
        itemCount++;
    });
</script>
@endsection
