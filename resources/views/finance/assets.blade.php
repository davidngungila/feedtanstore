@extends('layouts.app')

@section('page-title', 'Assets')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Assets</h2>
            <a href="{{ route('finance.assets.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Asset
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
                        <th class="px-4 py-3 text-left text-gray-700">Asset</th>
                        <th class="px-4 py-3 text-left text-gray-700">Type</th>
                        <th class="px-4 py-3 text-left text-gray-700">Purchase Date</th>
                        <th class="px-4 py-3 text-left text-gray-700">Cost</th>
                        <th class="px-4 py-3 text-left text-gray-700">Current Value</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($assets as $asset)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $asset->name }}</td>
                        <td class="px-4 py-3">{{ $asset->type }}</td>
                        <td class="px-4 py-3">{{ $asset->purchase_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-bold">TZS {{ number_format($asset->cost, 2) }}</td>
                        <td class="px-4 py-3 font-bold text-green-700">TZS {{ number_format($asset->current_value ?? $asset->cost, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $asset->status === 'Active' ? 'bg-green-100 text-green-800' : ($asset->status === 'Sold' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $asset->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('finance.assets.show', $asset) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('finance.assets.edit', $asset) }}" class="text-primary-600 hover:text-primary-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('finance.assets.destroy', $asset) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this asset?')">
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
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No assets yet! <a href="{{ route('finance.assets.create') }}" class="text-primary-600 hover:underline">Add your first asset!</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection