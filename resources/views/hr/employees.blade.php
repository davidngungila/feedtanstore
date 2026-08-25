@extends('layouts.app')

@section('page-title', 'Employees')

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
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h2 class="text-xl font-bold text-primary-900">Employees</h2>
            <div class="flex flex-wrap gap-2">
                <form method="GET" action="{{ route('hr.employees') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Search name, email, phone..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <select name="role" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ $roleFilter === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm"><i class="fas fa-search"></i></button>
                </form>
                @if($canManage)
                <button type="button" onclick="openEmployeeModal()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Employee
                </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Email</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Phone</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Role</th>
                        @if($canManage || $canDelete)<th class="px-4 py-3 text-right text-gray-700 font-medium">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($employees->count() > 0)
                        @foreach($employees as $index => $employee)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $employees->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $employee->name }} @if($employee->id === Auth::id())<span class="text-xs text-primary-600">(you)</span>@endif</td>
                                <td class="px-4 py-3 text-gray-600">{{ $employee->email }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $employee->phone ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="role-tag role-{{ $employee->role }}">{{ ucfirst(str_replace('_', ' ', $employee->role)) }}</span>
                                </td>
                                @if($canManage || $canDelete)
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if($canManage)
                                        <button type="button" onclick='openEmployeeModal(@json($employee->toArray()))' class="text-blue-600 hover:text-blue-800 font-medium text-sm mr-2">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                    @endif
                                    @if($canDelete && $employee->id !== Auth::id())
                                        <button type="button" data-url="{{ route('hr.employees.delete', $employee->id) }}" data-name="{{ $employee->name }}" data-role="{{ $employee->role }}" onclick="openDeleteEmployeeModal(this)" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ $canManage || $canDelete ? 6 : 5 }}" class="px-4 py-8 text-center text-gray-500">No employees found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $employees->links() }}</div>
    </div>
</div>

{{-- Add/Edit employee modal --}}
<div id="employeeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg my-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-primary-900" id="employeeModalTitle">Add Employee</h3>
                <button type="button" onclick="closeEmployeeModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <form id="employeeForm" action="" method="POST">
                @csrf
                <input type="hidden" name="_method" id="employeeFormMethod" value="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" id="emp_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" id="emp_email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" id="emp_phone" maxlength="20" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select name="role" id="emp_role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><span id="passwordLabel">Password *</span></label>
                        <input type="password" name="password" id="emp_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><span id="passwordConfirmLabel">Confirm Password *</span></label>
                        <input type="password" name="password_confirmation" id="emp_password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEmployeeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" id="employeeSubmitBtn" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Save Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete employee confirmation modal --}}
<div id="deleteEmployeeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-minus text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900">Delete Employee</h3>
            </div>
            <p class="text-gray-700 mb-1">Are you sure you want to delete <span id="delEmpName" class="font-bold text-primary-900"></span>?</p>
            <p class="text-sm text-gray-500 mb-5">Role: <span id="delEmpRole" class="font-semibold"></span>. This removes their account and access permanently.</p>
            <form id="deleteEmployeeForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteEmployeeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">Yes, Delete Employee</button>
            </form>
        </div>
    </div>
</div>

<script>
const employeeModal = document.getElementById('employeeModal');
const employeeForm = document.getElementById('employeeForm');
const deleteEmployeeModal = document.getElementById('deleteEmployeeModal');

function openEmployeeModal(employee = null) {
    employeeForm.action = employee ? '{{ url('hr/employees') }}/' + employee.id : '{{ route('hr.employees.store') }}';
    document.getElementById('employeeFormMethod').value = employee ? 'PUT' : 'POST';
    document.getElementById('employeeModalTitle').textContent = employee ? 'Edit Employee — ' + employee.name : 'Add Employee';
    document.getElementById('employeeSubmitBtn').textContent = employee ? 'Update Employee' : 'Create Employee';

    document.getElementById('emp_name').value = employee ? employee.name : '';
    document.getElementById('emp_email').value = employee ? employee.email : '';
    document.getElementById('emp_phone').value = employee && employee.phone ? employee.phone : '';
    document.getElementById('emp_role').value = employee ? employee.role : 'cashier';
    document.getElementById('emp_password').value = '';
    document.getElementById('emp_password_confirmation').value = '';

    const required = employee ? '' : ' *';
    document.getElementById('passwordLabel').textContent = employee ? 'New Password (leave blank to keep current)' : 'Password' + required;
    document.getElementById('passwordConfirmLabel').textContent = employee ? 'Confirm New Password' : 'Confirm Password' + required;
    document.getElementById('emp_password').required = !employee;
    document.getElementById('emp_password_confirmation').required = false;

    employeeModal.classList.remove('hidden');
}
function closeEmployeeModal() {
    employeeModal.classList.add('hidden');
    employeeForm.action = '';
}

function openDeleteEmployeeModal(btn) {
    document.getElementById('delEmpName').textContent = btn.dataset.name;
    document.getElementById('delEmpRole').textContent = btn.dataset.role.replace(/_/g, ' ');
    document.getElementById('deleteEmployeeForm').action = btn.dataset.url;
    deleteEmployeeModal.classList.remove('hidden');
}
function closeDeleteEmployeeModal() {
    deleteEmployeeModal.classList.add('hidden');
    document.getElementById('deleteEmployeeForm').action = '';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        if (!employeeModal.classList.contains('hidden')) closeEmployeeModal();
        if (!deleteEmployeeModal.contains(document.activeElement) && !deleteEmployeeModal.classList.contains('hidden')) closeDeleteEmployeeModal();
    }
});
employeeModal.addEventListener('click', function (e) { if (e.target === employeeModal) closeEmployeeModal(); });
deleteEmployeeModal.addEventListener('click', function (e) { if (e.target === deleteEmployeeModal) closeDeleteEmployeeModal(); });
</script>
@endsection
