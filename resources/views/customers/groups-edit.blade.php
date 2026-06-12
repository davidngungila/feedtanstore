@extends('layouts.app')

@section('page-title', 'Edit Customer Group')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Customer Group</h2>
            <a href="{{ route('customers.groups') }}" class="text-primary-600 hover:text-primary-800">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        <form action="{{ route('customers.groups.update', $group) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ $group->name }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ $group->description }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount Percentage</label>
                    <input type="number" name="discount_percentage" value="{{ $group->discount_percentage }}" min="0" max="100" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('customers.groups') }}" class="px-4 py-2 border border-gray-300 rounded-lg">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg">Update Group</button>
            </div>
        </form>
    </div>
</div>
@endsection