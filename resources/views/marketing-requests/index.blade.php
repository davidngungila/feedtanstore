@extends('layouts.app')

@section('page-title', 'Marketing Requests')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Marketing Requests</h1>
        <a href="{{ route('marketing-requests.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>New Request
        </a>
    </div>

    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Request Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Requested By</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($marketingRequests as $request)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $request->request_number }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $request->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $request->requester->name }}</td>
                        <td class="px-6 py-4">
                            @if($request->status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($request->status === 'accepted')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Processing</span>
                            @elseif($request->status === 'processed')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Processed</span>
                            @elseif($request->status === 'rejected')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $request->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('marketing-requests.show', $request) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-bullhorn text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">No marketing requests found</p>
                            <p class="text-sm">Get started by creating a new request.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $marketingRequests->links() }}
        </div>
    </div>
</div>
@endsection
