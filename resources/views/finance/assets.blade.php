@extends('layouts.app')

@section('page-title', 'Assets')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Assets</h2>
            <button class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                Add Asset
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600">Asset</th>
                        <th class="px-4 py-3 text-left text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left text-gray-600">Purchase Date</th>
                        <th class="px-4 py-3 text-left text-gray-600">Cost</th>
                        <th class="px-4 py-3 text-left text-gray-600">Current Value</th>
                        <th class="px-4 py-3 text-left text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-3 font-semibold">POS System</td>
                        <td class="px-4 py-3">Equipment</td>
                        <td class="px-4 py-3">01/01/2024</td>
                        <td class="px-4 py-3 font-bold">TZS 500,000.00</td>
                        <td class="px-4 py-3 font-bold text-green-700">TZS 450,000.00</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Active</span></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold">Delivery Van</td>
                        <td class="px-4 py-3">Vehicle</td>
                        <td class="px-4 py-3">15/03/2024</td>
                        <td class="px-4 py-3 font-bold">TZS 12,000,000.00</td>
                        <td class="px-4 py-3 font-bold text-green-700">TZS 11,500,000.00</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Active</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection