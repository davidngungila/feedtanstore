@extends('layouts.app')

@section('page-title', 'Assets')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Assets</h2>
            <div class="flex gap-2">
                <form action="{{ route('finance.assets.run-depreciation') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Run depreciation calculation for all active assets?')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-calculator mr-2"></i>Run Depreciation
                    </button>
                </form>
                <a href="{{ route('finance.assets.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Asset
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
                        <th class="px-4 py-3 text-left text-gray-600">Asset</th>
                        <th class="px-4 py-3 text-left text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left text-gray-600">Purchase Date</th>
                        <th class="px-4 py-3 text-left text-gray-600">Cost</th>
                        <th class="px-4 py-3 text-left text-gray-600">Current Value</th>
                        <th class="px-4 py-3 text-left text-gray-600">Depreciation %</th>
                        <th class="px-4 py-3 text-left text-gray-600">Remaining Life</th>
                        <th class="px-4 py-3 text-left text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($assets as $asset)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $asset->name }}</td>
                        <td class="px-4 py-3">{{ $asset->type }}</td>
                        <td class="px-4 py-3">{{ $asset->purchase_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-bold">TZS {{ number_format($asset->purchase_cost, 2) }}</td>
                        <td class="px-4 py-3 font-bold text-green-700">TZS {{ number_format($asset->current_value, 2) }}</td>
                        <td class="px-4 py-3 text-orange-600">{{ number_format($asset->depreciation_percentage, 1) }}%</td>
                        <td class="px-4 py-3">{{ $asset->remaining_life }} years</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $asset->status === 'active' ? 'bg-green-100 text-green-800' : ($asset->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($asset->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('finance.assets.show', $asset) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('finance.assets.edit', $asset) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('finance.assets.destroy', $asset) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            No assets found. <a href="{{ route('finance.assets.create') }}" class="text-primary-600 hover:underline">Add your first asset</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection