@extends('layouts.app')

@section('page-title', 'Chart of Accounts')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Chart of Accounts</h2>
            <a href="{{ route('finance.chart-of-accounts.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Account
            </a>
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
                        <th class="text-left">Account Code</th>
                        <th class="text-left">Name</th>
                        <th class="text-left">Type</th>
                        <th class="text-left">Parent Account</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('finance.chart-of-accounts.show', $account) }}" class="hover:underline">{{ $account->account_code }}</a>
                        </td>
                        <td class="text-gray-600">{{ $account->name }}</td>
                        <td>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ 
                                $account->type === 'Asset' ? 'bg-blue-100 text-blue-800' :
                                ($account->type === 'Liability' ? 'bg-orange-100 text-orange-800' :
                                ($account->type === 'Equity' ? 'bg-purple-100 text-purple-800' :
                                ($account->type === 'Revenue' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')))
                            }}">
                                {{ $account->type }}
                            </span>
                        </td>
                        <td class="text-gray-600">{{ $account->parent->name ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $account->is_active ? 'badge-green' : 'badge-gray' }}">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('finance.chart-of-accounts.show', $account) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('finance.chart-of-accounts.edit', $account) }}" class="text-primary-600 hover:text-primary-800 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('finance.chart-of-accounts.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this account?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
