@extends('layouts.app')

@section('page-title', 'Shifts')

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

    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Work Shifts</h2>
            @if($canManage)
            <button type="button" onclick='openShiftModal()' class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Shift
            </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Start Time</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">End Time</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Description</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                        @if($canManage || $canDelete)<th class="px-4 py-3 text-right text-gray-700 font-medium">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($shifts->count() > 0)
                        @foreach($shifts as $index => $shift)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $shifts->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $shift->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-gray-600 max-w-[240px] truncate">{{ $shift->description ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($canManage)
                                        <button type="button" data-url="{{ route('hr.shifts.toggle', $shift->id) }}" data-name="{{ $shift->name }}" data-active="{{ $shift->is_active ? 1 : 0 }}" onclick="openToggleShiftModal(this)" class="px-3 py-1 rounded-full text-xs font-semibold {{ $shift->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                            {{ $shift->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $shift->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $shift->is_active ? 'Active' : 'Inactive' }}</span>
                                    @endif
                                </td>
                                @if($canManage || $canDelete)
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if($canManage)
                                        @php($shiftPayload = [
                                            'id' => $shift->id,
                                            'name' => $shift->name,
                                            'start_time' => substr($shift->start_time, 0, 5),
                                            'end_time' => substr($shift->end_time, 0, 5),
                                            'description' => $shift->description,
                                            'is_active' => (bool) $shift->is_active,
                                        ])
                                        <button type="button" onclick='openShiftModal(@json($shiftPayload))' class="text-blue-600 hover:text-blue-800 font-medium text-sm mr-2">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                    @endif
                                    @if($canDelete)
                                        <button type="button" data-url="{{ route('hr.shifts.delete', $shift->id) }}" data-name="{{ $shift->name }}" onclick="openDeleteShiftModal(this)" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ $canManage || $canDelete ? 7 : 6 }}" class="px-4 py-8 text-center text-gray-500">No shifts found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $shifts->links() }}</div>
    </div>
</div>

{{-- Add/Edit shift modal --}}
<div id="shiftModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg my-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-primary-900" id="shiftModalTitle">Add Shift</h3>
                <button type="button" onclick="closeShiftModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <form id="shiftForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shift Name *</label>
                        <input type="text" name="name" id="shift_name" required maxlength="255" placeholder="e.g., Morning Shift" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                            <input type="time" name="start_time" id="shift_start" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                            <input type="time" name="end_time" id="shift_end" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="shift_description" rows="2" maxlength="1000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" id="shift_is_active" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        Active (available for assignment)
                    </label>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeShiftModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" id="shiftSubmitBtn" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Save Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toggle shift confirmation modal --}}
<div id="toggleShiftModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-arrows-rotate text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900" id="toggleShiftTitle">Change Shift Status</h3>
            </div>
            <p class="text-gray-700 mb-5" id="toggleShiftText"></p>
            <form id="toggleShiftForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                <button type="button" onclick="closeToggleShiftModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" id="toggleShiftConfirm" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">Yes, Change Status</button>
            </form>
        </div>
    </div>
</div>

{{-- Delete shift confirmation modal --}}
<div id="deleteShiftModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock-rotate-left text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900">Delete Shift</h3>
            </div>
            <p class="text-gray-700 mb-5">Are you sure you want to delete the shift <span id="delShiftName" class="font-bold text-primary-900"></span>? This cannot be undone.</p>
            <form id="deleteShiftForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteShiftModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">Yes, Delete Shift</button>
            </form>
        </div>
    </div>
</div>

<script>
const shiftModal = document.getElementById('shiftModal');
const shiftForm = document.getElementById('shiftForm');
const toggleShiftModal = document.getElementById('toggleShiftModal');
const deleteShiftModal = document.getElementById('deleteShiftModal');

function openShiftModal(shift = null) {
    shiftForm.action = shift ? '{{ url('hr/shifts') }}/' + shift.id : '{{ route('hr.shifts.store') }}';
    document.getElementById('shiftModalTitle').textContent = shift ? 'Edit Shift — ' + shift.name : 'Add Shift';
    document.getElementById('shiftSubmitBtn').textContent = shift ? 'Update Shift' : 'Create Shift';

    document.getElementById('shift_name').value = shift ? shift.name : '';
    document.getElementById('shift_start').value = shift ? shift.start_time : '';
    document.getElementById('shift_end').value = shift ? shift.end_time : '';
    document.getElementById('shift_description').value = shift && shift.description ? shift.description : '';
    document.getElementById('shift_is_active').checked = shift ? !!shift.is_active : true;

    shiftModal.classList.remove('hidden');
}
function closeShiftModal() {
    shiftModal.classList.add('hidden');
    shiftForm.action = '';
}

function openToggleShiftModal(btn) {
    const active = btn.dataset.active === '1';
    document.getElementById('toggleShiftForm').action = btn.dataset.url;
    document.getElementById('toggleShiftText').innerHTML = active
        ? 'Deactivate the shift <strong>' + btn.dataset.name + '</strong>? It will no longer be available for new assignments.'
        : 'Activate the shift <strong>' + btn.dataset.name + '</strong>?';
    document.getElementById('toggleShiftConfirm').textContent = active ? 'Yes, Deactivate' : 'Yes, Activate';
    toggleShiftModal.classList.remove('hidden');
}
function closeToggleShiftModal() {
    toggleShiftModal.classList.add('hidden');
    document.getElementById('toggleShiftForm').action = '';
}

function openDeleteShiftModal(btn) {
    document.getElementById('delShiftName').textContent = btn.dataset.name;
    document.getElementById('deleteShiftForm').action = btn.dataset.url;
    deleteShiftModal.classList.remove('hidden');
}
function closeDeleteShiftModal() {
    deleteShiftModal.classList.add('hidden');
    document.getElementById('deleteShiftForm').action = '';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        if (!shiftModal.classList.contains('hidden')) closeShiftModal();
        if (!toggleShiftModal.classList.contains('hidden')) closeToggleShiftModal();
        if (!deleteShiftModal.classList.contains('hidden')) closeDeleteShiftModal();
    }
});
[shiftModal, toggleShiftModal, deleteShiftModal].forEach(function (m) {
    m.addEventListener('click', function (e) { if (e.target === m) m.classList.add('hidden'); });
});
</script>
@endsection
