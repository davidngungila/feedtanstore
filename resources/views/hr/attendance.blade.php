@extends('layouts.app')

@section('page-title', 'Attendance')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">My Attendance Today</h2>
        <div class="flex gap-4 flex-wrap">
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
                    <span class="font-medium text-gray-700"><i class="fas fa-check-circle text-green-600 mr-2"></i>Attendance Complete</span>
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
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-bold text-primary-900">Attendance Records</h2>
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('hr.attendance') }}" class="flex flex-wrap gap-2">
                    <input type="date" name="date" value="{{ $filters['date'] }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Statuses</option>
                        @foreach(['present', 'late', 'half-day', 'absent'] as $s)
                            <option value="{{ $s }}" {{ $filters['status'] === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <select name="user_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Employees</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $filters['user_id'] == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm"><i class="fas fa-search"></i></button>
                </form>
                @if($canManage)
                <button type="button" onclick="openAttendanceModal()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Record
                </button>
                @endif
            </div>
        </div>

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
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Notes</th>
                        @if($canManage || $canDelete)<th class="px-4 py-3 text-right text-gray-700 font-medium">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($attendances->count() > 0)
                        @foreach($attendances as $index => $attendance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $attendances->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $attendance->user->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->date->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $attendance->status == 'present' ? 'bg-green-100 text-green-800' : ($attendance->status == 'late' ? 'bg-yellow-100 text-yellow-800' : ($attendance->status == 'half-day' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 max-w-[200px] truncate">{{ $attendance->notes ?? '-' }}</td>
                                @if($canManage || $canDelete)
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if($canManage)
                                        @php($attPayload = [
                                            'id' => $attendance->id,
                                            'user_id' => $attendance->user_id,
                                            'date' => $attendance->date->toDateString(),
                                            'status' => $attendance->status,
                                            'check_in' => $attendance->check_in ? $attendance->check_in->format('H:i') : '',
                                            'check_out' => $attendance->check_out ? $attendance->check_out->format('H:i') : '',
                                            'notes' => $attendance->notes,
                                        ])
                                        <button type="button" onclick='openAttendanceModal(@json($attPayload))' class="text-blue-600 hover:text-blue-800 font-medium text-sm mr-2">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                    @endif
                                    @if($canDelete)
                                        <button type="button" data-url="{{ route('hr.attendance.destroy', $attendance->id) }}" data-name="{{ $attendance->user->name ?? 'employee' }}" data-date="{{ $attendance->date->format('M d, Y') }}" onclick="openDeleteAttendanceModal(this)" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ $canManage || $canDelete ? 8 : 7 }}" class="px-4 py-8 text-center text-gray-500">No attendance records found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $attendances->links() }}</div>
    </div>
</div>

{{-- Add/Edit attendance modal --}}
<div id="attendanceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg my-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-primary-900" id="attendanceModalTitle">Add Attendance Record</h3>
                <button type="button" onclick="closeAttendanceModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <form id="attendanceForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee *</label>
                            <select name="user_id" id="att_user" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Employee</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                            <input type="date" name="date" id="att_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" id="att_status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                @foreach(['present', 'late', 'half-day', 'absent'] as $s)
                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check In</label>
                            <input type="time" name="check_in" id="att_check_in" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check Out</label>
                            <input type="time" name="check_out" id="att_check_out" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" id="att_notes" rows="2" maxlength="1000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAttendanceModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" id="attendanceSubmitBtn" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete attendance confirmation modal --}}
<div id="deleteAttendanceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-xmark text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900">Delete Attendance Record</h3>
            </div>
            <p class="text-gray-700 mb-5">Are you sure you want to delete the attendance record of <span id="delAttName" class="font-bold text-primary-900"></span> for <span id="delAttDate" class="font-semibold"></span>?</p>
            <form id="deleteAttendanceForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteAttendanceModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">Yes, Delete Record</button>
            </form>
        </div>
    </div>
</div>

<script>
const attendanceModal = document.getElementById('attendanceModal');
const attendanceForm = document.getElementById('attendanceForm');
const deleteAttendanceModal = document.getElementById('deleteAttendanceModal');

function openAttendanceModal(record = null) {
    attendanceForm.action = record ? '{{ url('hr/attendance') }}/' + record.id : '{{ route('hr.attendance.store') }}';
    document.getElementById('attendanceModalTitle').textContent = record ? 'Edit Attendance Record' : 'Add Attendance Record';
    document.getElementById('attendanceSubmitBtn').textContent = record ? 'Update Record' : 'Add Record';

    document.getElementById('att_user').value = record ? record.user_id : '';
    document.getElementById('att_date').value = record ? record.date : new Date().toISOString().slice(0, 10);
    document.getElementById('att_status').value = record ? record.status : 'present';
    document.getElementById('att_check_in').value = record && record.check_in ? record.check_in : '';
    document.getElementById('att_check_out').value = record && record.check_out ? record.check_out : '';
    document.getElementById('att_notes').value = record && record.notes ? record.notes : '';

    attendanceModal.classList.remove('hidden');
}
function closeAttendanceModal() {
    attendanceModal.classList.add('hidden');
    attendanceForm.action = '';
}

function openDeleteAttendanceModal(btn) {
    document.getElementById('delAttName').textContent = btn.dataset.name;
    document.getElementById('delAttDate').textContent = btn.dataset.date;
    document.getElementById('deleteAttendanceForm').action = btn.dataset.url;
    deleteAttendanceModal.classList.remove('hidden');
}
function closeDeleteAttendanceModal() {
    deleteAttendanceModal.classList.add('hidden');
    document.getElementById('deleteAttendanceForm').action = '';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        if (!attendanceModal.classList.contains('hidden')) closeAttendanceModal();
        if (!deleteAttendanceModal.classList.contains('hidden')) closeDeleteAttendanceModal();
    }
});
attendanceModal.addEventListener('click', function (e) { if (e.target === attendanceModal) closeAttendanceModal(); });
deleteAttendanceModal.addEventListener('click', function (e) { if (e.target === deleteAttendanceModal) closeDeleteAttendanceModal(); });
</script>
@endsection
