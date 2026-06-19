@extends('layouts.app')

@section('page-title', $account->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $account->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('finance.chart-of-accounts.edit', $account) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('finance.chart-of-accounts') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Chart of Accounts
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Account Code</p>
                <p class="font-medium">{{ $account->account_code }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Account Type</p>
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ 
                    $account->type === 'Asset' ? 'bg-blue-100 text-blue-800' :
                    ($account->type === 'Liability' ? 'bg-orange-100 text-orange-800' :
                    ($account->type === 'Equity' ? 'bg-purple-100 text-purple-800' :
                    ($account->type === 'Revenue' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')))
                }}">
                    {{ $account->type }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Parent Account</p>
                <p class="font-medium">{{ $account->parent->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="badge {{ $account->is_active ? 'badge-green' : 'badge-gray' }}">
                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Description</p>
                <p>{{ $account->description ?? '-' }}</p>
            </div>
        </div>
    </div>

    @if($account->children->count() > 0)
    <div class="card rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Sub-Accounts</h3>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Account Code</th>
                        <th class="text-left">Name</th>
                        <th class="text-left">Type</th>
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($account->children as $child)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('finance.chart-of-accounts.show', $child) }}" class="hover:underline">{{ $child->account_code }}</a>
                        </td>
                        <td class="text-gray-600">{{ $child->name }}</td>
                        <td class="text-gray-600">{{ $child->type }}</td>
                        <td>
                            <span class="badge {{ $child->is_active ? 'badge-green' : 'badge-gray' }}">
                                {{ $child->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($account->accountingEntries->count() > 0)
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Accounting Entries</h3>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Date</th>
                        <th class="text-left">Reference</th>
                        <th class="text-left">Type</th>
                        <th class="text-left">Amount</th>
                        <th class="text-left">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($account->accountingEntries as $entry)
                    <tr>
                        <td class="text-gray-600">{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-gray-600">{{ $entry->reference_number }}</td>
                        <td>
                            <span class="badge {{ $entry->type === 'debit' ? 'badge-red' : 'badge-green' }}">
                                {{ ucfirst($entry->type) }}
                            </span>
                        </td>
                        <td class="font-medium">TZS {{ number_format($entry->amount, 2) }}</td>
                        <td class="text-gray-600">{{ $entry->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
