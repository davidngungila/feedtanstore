@extends('layouts.app')

@section('page-title', 'Attendance')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Today's Attendance</h2>
        <div class="flex gap-4">
            @if(!$todayAttendance)
                <form action="{{ route('hr.attendance.check-in') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>Check In
                    </button>
                </form>
            @elseif(!$todayAttendance->check_out)
                <form action="{{ route('hr.attendance.check-out') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Check Out
                    </button>
                </form>
            @else
                <div class="px-6 py-3 bg-gray-100 rounded-lg">
                    <span class="font-medium text-gray-700">Attendance Complete</span>
                </div>
            @endif
        </div>

        @if($todayAttendance)
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Check In</span>
                    <p class="text-lg font-semibold text-gray-900">{{ $todayAttendance->check_in ? $todayAttendance->check_in->format('h:i A') : '-' }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Check Out</span>
                    <p class="text-lg font-semibold text-gray-900">{{ $todayAttendance->check_out ? $todayAttendance->check_out->format('h:i A') : '-' }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Status</span>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst($todayAttendance->status) }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Attendance Records</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Employee</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Check In</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Check Out</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($attendances->count() > 0)
                        @foreach($attendances as $index => $attendance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $attendances->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $attendance->user->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->date->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $attendance->status == 'present' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No attendance records found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection
