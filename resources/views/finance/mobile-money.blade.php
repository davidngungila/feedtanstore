@extends('layouts.app')

@section('page-title', 'Mobile Money Reconciliation')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Mobile Money Reconciliation</h2>
            <a href="{{ route('finance.mobile-money.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Mobile Money Account
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Provider</th>
                        <th class="px-4 py-3 text-left text-gray-700">Phone Number</th>
                        <th class="px-4 py-3 text-left text-gray-700">Account Name</th>
                        <th class="px-4 py-3 text-left text-gray-700">Balance</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($mobileMoneyAccounts as $account)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $account->provider }}</td>
                        <td class="px-4 py-3">{{ $account->phone_number }}</td>
                        <td class="px-4 py-3">{{ $account->account_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 font-bold text-green-700">TZS {{ number_format($account->balance, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($account->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('finance.mobile-money.edit', $account) }}" class="text-primary-600 hover:text-primary-800 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('finance.mobile-money.destroy', $account) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No mobile money accounts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection