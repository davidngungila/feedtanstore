@extends('layouts.app')

@section('page-title', 'Income Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Income Details</h2>
            <a href="{{ route('finance.income') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Income
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-600 mb-1">Reference Number</div>
                <div class="font-semibold text-lg">{{ $income->reference_number }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Date</div>
                <div class="font-semibold">{{ $income->date->format('l, d F Y') }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Category</div>
                <div class="font-semibold">{{ $income->category }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Amount</div>
                <div class="font-bold text-green-700 text-2xl">TZS {{ number_format($income->amount, 2) }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Payment Method</div>
                <div class="font-semibold">{{ $income->payment_method }}</div>
            </div>
            @if($income->bankAccount)
            <div>
                <div class="text-sm text-gray-600 mb-1">Bank Account</div>
                <div class="font-semibold">{{ $income->bankAccount->name }} - {{ $income->bankAccount->bank_name }}</div>
            </div>
            @endif
            @if($income->mobileMoneyAccount)
            <div>
                <div class="text-sm text-gray-600 mb-1">Mobile Money Account</div>
                <div class="font-semibold">{{ $income->mobileMoneyAccount->provider }} - {{ $income->mobileMoneyAccount->phone_number }}</div>
            </div>
            @endif
            <div>
                <div class="text-sm text-gray-600 mb-1">Added By</div>
                <div class="font-semibold">{{ $income->user->name }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-sm text-gray-600 mb-1">Description</div>
                <div class="font-semibold">{{ $income->description ?? 'No description' }}</div>
            </div>
        </div>

        <div class="mt-8 flex gap-3">
            <a href="{{ route('finance.income.edit', $income) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit Income
            </a>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-4">Accounting Entries</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Account</th>
                        <th class="px-4 py-3 text-left text-gray-700">Type</th>
                        <th class="px-4 py-3 text-left text-gray-700">Amount</th>
                        <th class="px-4 py-3 text-left text-gray-700">Description</th>
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($entries as $entry)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $entry->account }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $entry->type === 'debit' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ strtoupper($entry->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-bold">TZS {{ number_format($entry->amount, 2) }}</td>
                        <td class="px-4 py-3">{{ $entry->description }}</td>
                        <td class="px-4 py-3">{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
