@extends('layouts.app')

@section('page-title', 'Customers')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('rider.dashboard') }}" class="text-primary-600 hover:text-primary-800 mb-2 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <h2 class="text-xl font-bold text-primary-900">Customers</h2>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Name</th>
                        <th class="px-4 py-3 text-left text-gray-700">Phone</th>
                        <th class="px-4 py-3 text-left text-gray-700">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($customers as $customer)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $customer->customer_name }}</td>
                        <td class="px-4 py-3">
                            <a href="tel:{{ $customer->customer_phone }}" class="text-primary-600 hover:text-primary-800">
                                {{ $customer->customer_phone }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            @if($customer->customer_email)
                            <a href="mailto:{{ $customer->customer_email }}" class="text-primary-600 hover:text-primary-800">
                                {{ $customer->customer_email }}
                            </a>
                            @else
                            <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                            No customers found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
