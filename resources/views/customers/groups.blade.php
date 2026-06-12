@extends('layouts.app')

@section('page-title', 'Customer Groups')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Customer Groups</h2>
            <a href="{{ route('customers.groups.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>New Group
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
                        <th class="text-left">Name</th>
                        <th class="text-left">Description</th>
                        <th class="text-left">Discount</th>
                        <th class="text-left">Customers</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $group->name }}</td>
                        <td class="text-gray-600">{{ $group->description ?? '-' }}</td>
                        <td class="text-gray-600">{{ $group->discount_percentage }}%</td>
                        <td class="text-gray-600">{{ $group->customers_count }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('customers.groups.edit', $group) }}" class="text-primary-600 hover:text-primary-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('customers.groups.destroy', $group) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this group?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
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