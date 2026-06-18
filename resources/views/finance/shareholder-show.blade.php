@extends('layouts.app')

@section('page-title', $shareholder->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $shareholder->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('finance.shareholders.edit', $shareholder) }}" class="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="{{ route('finance.shareholders.add-share', $shareholder) }}" class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                    <i class="fas fa-plus mr-1"></i>Add Shares
                </a>
                <a href="{{ route('finance.shareholders') }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>

        <!-- Shareholder Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Email</p>
                <p class="font-medium">{{ $shareholder->email ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Phone</p>
                <p class="font-medium">{{ $shareholder->phone ?? 'N/A' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 mb-1">Address</p>
                <p class="font-medium">{{ $shareholder->address ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Shares</p>
                <p class="font-bold text-primary-700 text-xl">{{ number_format($shareholder->total_shares) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Investment</p>
                <p class="font-bold text-green-700 text-xl">TZS {{ number_format($shareholder->total_investment, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Shares History -->
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-4">Shares History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-gray-600">Number of Shares</th>
                        <th class="px-4 py-3 text-left text-gray-600">Share Price</th>
                        <th class="px-4 py-3 text-left text-gray-600">Total Amount</th>
                        <th class="px-4 py-3 text-left text-gray-600">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($shareholder->shares as $share)
                        <tr>
                            <td class="px-4 py-3">{{ $share->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 font-bold text-primary-700">{{ number_format($share->number_of_shares) }}</td>
                            <td class="px-4 py-3">TZS {{ number_format($share->share_price, 2) }}</td>
                            <td class="px-4 py-3 font-bold text-green-700">TZS {{ number_format($share->total_amount, 2) }}</td>
                            <td class="px-4 py-3">{{ $share->description ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No shares yet!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
