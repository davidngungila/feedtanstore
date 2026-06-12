@extends('layouts.app')

@section('page-title', 'New Customer')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">New Customer</h2>
            <a href="{{ route('customers.list') }}" class="text-primary-600 hover:text-primary-800">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Group</label>
                    <select name="customer_group_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Group...</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->discount_percentage }}% off)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Credit Limit</label>
                    <input type="number" name="credit_limit" value="0" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('customers.list') }}" class="px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg">Create Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
