@extends('layouts.app')

@section('page-title', 'Shareholders Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Shareholders</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $shareholders->count() }}</p>
        </div>
        
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Shares</h3>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($totalShares) }}</p>
        </div>
        
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Investment</h3>
            <p class="text-3xl font-bold text-gray-800">TZS {{ number_format($totalInvestment, 2) }}</p>
        </div>
    </div>
    
    <!-- Shareholders List -->
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <h2 class="text-xl font-bold text-primary-900">Shareholders</h2>
            <div class="flex items-center gap-2 flex-wrap">
                <form action="{{ route('finance.shareholders.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-file-import mr-2"></i>Import
                    </button>
                </form>
                <a href="{{ route('finance.shareholders.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Shareholder
                </a>
            </div>
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
                        <th class="px-4 py-3 text-left text-gray-600">Shareholding No.</th>
                        <th class="px-4 py-3 text-left text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left text-gray-600">Email</th>
                        <th class="px-4 py-3 text-left text-gray-600">Phone</th>
                        <th class="px-4 py-3 text-left text-gray-600">Total Shares</th>
                        <th class="px-4 py-3 text-left text-gray-600">Total Investment</th>
                        <th class="px-4 py-3 text-left text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($shareholders as $shareholder)
                        <tr>
                            <td class="px-4 py-3 font-medium text-primary-600">{{ $shareholder->shareholding_number }}</td>
                            <td class="px-4 py-3 font-medium">{{ $shareholder->name }}</td>
                            <td class="px-4 py-3">{{ $shareholder->email ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $shareholder->phone ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-bold text-primary-700">{{ number_format($shareholder->total_shares) }}</td>
                            <td class="px-4 py-3 font-bold text-green-700">TZS {{ number_format($shareholder->total_investment, 2) }}</td>
                            <td class="px-4 py-3 flex items-center gap-2">
                                <a href="{{ route('finance.shareholders.show', $shareholder) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('finance.shareholders.edit', $shareholder) }}" class="text-primary-600 hover:text-primary-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('finance.shareholders.add-share', $shareholder) }}" class="text-green-600 hover:text-green-800" title="Add Shares">
                                    <i class="fas fa-plus-circle"></i>
                                </a>
                                <form action="{{ route('finance.shareholders.destroy', $shareholder) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this shareholder?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No shareholders yet!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
