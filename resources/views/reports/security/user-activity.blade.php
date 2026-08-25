@extends('layouts.app')

@section('page-title', 'User Activity')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>User Activity</h2>
            <form method="GET" action="{{ route('reports.security.user-activity') }}" class="flex flex-wrap items-center gap-2">
                <select name="user_id" class="form-input input-field px-3 py-2">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.security.user-activity.download', array_filter(['user_id' => request('user_id'), 'start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)])) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        @if($logs->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-user-clock text-4xl mb-3"></i>
            <p>No activity found for this user in this period.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date &amp; Time</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">User</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Action</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $log->user->name ?? 'System' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 text-primary-700">{{ $log->action }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $log->details ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
