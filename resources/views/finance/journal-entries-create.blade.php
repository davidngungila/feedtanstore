@extends('layouts.app')

@section('page-title', 'Create Journal Entry')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Create Journal Entry</h2>
            <a href="{{ route('journal-entries.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Journal Entries
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

        <form action="{{ route('journal-entries.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Entry Date *</label>
                    <input type="date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <input type="text" name="description" value="{{ old('description') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Journal Entry Lines</h3>
                    <button type="button" id="addLineBtn" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Line
                    </button>
                </div>

                <div id="journalLines" class="space-y-4">
                    <div class="journal-line grid grid-cols-1 md:grid-cols-5 gap-4 items-end border border-gray-200 rounded-lg p-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account *</label>
                            <select name="lines[0][account_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }} ({{ $account->type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <select name="lines[0][type]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                            <input type="number" name="lines[0][amount]" required min="0.01" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                            <input type="text" name="lines[0][description]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="md:col-span-1">
                            <button type="button" class="remove-line-btn px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm disabled:opacity-50" disabled>Remove</button>
                        </div>
                    </div>

                    <div class="journal-line grid grid-cols-1 md:grid-cols-5 gap-4 items-end border border-gray-200 rounded-lg p-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account *</label>
                            <select name="lines[1][account_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }} ({{ $account->type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <select name="lines[1][type]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                            <input type="number" name="lines[1][amount]" required min="0.01" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                            <input type="text" name="lines[1][description]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="md:col-span-1">
                            <button type="button" class="remove-line-btn px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm">Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('journal-entries.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Create Journal Entry
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let lineIndex = 2;

document.getElementById('addLineBtn').addEventListener('click', function() {
    const journalLines = document.getElementById('journalLines');
    const newLine = document.createElement('div');
    newLine.className = 'journal-line grid grid-cols-1 md:grid-cols-5 gap-4 items-end border border-gray-200 rounded-lg p-4';
    newLine.innerHTML = `
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Account *</label>
            <select name="lines[${lineIndex}][account_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }} ({{ $account->type }})</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
            <select name="lines[${lineIndex}][type]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="debit">Debit</option>
                <option value="credit">Credit</option>
            </select>
        </div>
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
            <input type="number" name="lines[${lineIndex}][amount]" required min="0.01" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
            <input type="text" name="lines[${lineIndex}][description]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div class="md:col-span-1">
            <button type="button" class="remove-line-btn px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm">Remove</button>
        </div>
    `;
    journalLines.appendChild(newLine);
    lineIndex++;
    updateRemoveButtons();
});

document.getElementById('journalLines').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-line-btn')) {
        e.target.closest('.journal-line').remove();
        updateRemoveButtons();
    }
});

function updateRemoveButtons() {
    const lines = document.querySelectorAll('.journal-line');
    lines.forEach((line, index) => {
        const btn = line.querySelector('.remove-line-btn');
        btn.disabled = lines.length <= 2;
    });
}
</script>
@endsection
