<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\WorkShift;
use App\Models\ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HRController extends Controller
{
    // Employees
    public function employees()
    {
        $employees = User::latest()->paginate(20);
        return view('hr.employees', compact('employees'));
    }

    public function createEmployee()
    {
        $roles = ['admin', 'cashier', 'manager', 'accountant', 'auditor'];
        return view('hr.employees-create', compact('roles'));
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_employee',
            'details' => 'Created new employee: ' . $user->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('hr.employees')->with('success', 'Employee created successfully!');
    }

    public function editEmployee($id)
    {
        $employee = User::findOrFail($id);
        $roles = ['admin', 'cashier', 'manager', 'accountant', 'auditor'];
        return view('hr.employees-edit', compact('employee', 'roles'));
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,
            'role' => 'required|string',
        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $employee->update(['password' => Hash::make($request->password)]);
        }

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_employee',
            'details' => 'Updated employee: ' . $employee->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('hr.employees')->with('success', 'Employee updated successfully!');
    }

    public function deleteEmployee($id)
    {
        $employee = User::findOrFail($id);
        $employeeName = $employee->name;
        $employee->delete();

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_employee',
            'details' => 'Deleted employee: ' . $employeeName,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('hr.employees')->with('success', 'Employee deleted successfully!');
    }

    // Roles & Permissions
    public function roles()
    {
        $roles = ['admin', 'cashier', 'manager', 'accountant', 'auditor'];
        $permissions = [
            'sales' => ['create', 'read', 'update', 'delete'],
            'inventory' => ['create', 'read', 'update', 'delete'],
            'purchasing' => ['create', 'read', 'update', 'delete'],
            'hr' => ['create', 'read', 'update', 'delete'],
            'finance' => ['create', 'read', 'update', 'delete'],
        ];
        return view('hr.roles', compact('roles', 'permissions'));
    }

    // Attendance
    public function attendance()
    {
        $attendances = Attendance::with('user')->latest()->paginate(30);
        $today = today()->toDateString();
        $todayAttendance = Attendance::where('user_id', Auth::id())->where('date', $today)->first();
        return view('hr.attendance', compact('attendances', 'todayAttendance'));
    }

    public function checkIn()
    {
        $today = today()->toDateString();
        $existing = Attendance::where('user_id', Auth::id())->where('date', $today)->first();

        if ($existing) {
            return back()->with('error', 'You have already checked in today!');
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'date' => $today,
            'check_in' => now(),
            'status' => 'present',
        ]);

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'check_in',
            'details' => Auth::user()->name . ' checked in',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Checked in successfully!');
    }

    public function checkOut()
    {
        $today = today()->toDateString();
        $attendance = Attendance::where('user_id', Auth::id())->where('date', $today)->first();

        if (!$attendance) {
            return back()->with('error', 'You need to check in first!');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'You have already checked out today!');
        }

        $attendance->update(['check_out' => now()]);

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'check_out',
            'details' => Auth::user()->name . ' checked out',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Checked out successfully!');
    }

    // Work Shifts
    public function shifts()
    {
        $shifts = WorkShift::latest()->paginate(20);
        return view('hr.shifts', compact('shifts'));
    }

    public function createShift()
    {
        return view('hr.shifts-create');
    }

    public function storeShift(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'nullable|string',
        ]);

        WorkShift::create($request->all());

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_shift',
            'details' => 'Created shift: ' . $request->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('hr.shifts')->with('success', 'Shift created successfully!');
    }

    public function editShift($id)
    {
        $shift = WorkShift::findOrFail($id);
        return view('hr.shifts-edit', compact('shift'));
    }

    public function updateShift(Request $request, $id)
    {
        $shift = WorkShift::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'nullable|string',
        ]);

        $shift->update($request->all());

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_shift',
            'details' => 'Updated shift: ' . $shift->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('hr.shifts')->with('success', 'Shift updated successfully!');
    }

    public function toggleShift($id)
    {
        $shift = WorkShift::findOrFail($id);
        $shift->update(['is_active' => !$shift->is_active]);

        return back()->with('success', 'Shift status updated!');
    }

    public function deleteShift($id)
    {
        $shift = WorkShift::findOrFail($id);
        $shiftName = $shift->name;
        $shift->delete();

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_shift',
            'details' => 'Deleted shift: ' . $shiftName,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Shift deleted successfully!');
    }

    // Activity Logs
    public function activity()
    {
        $logs = ActionLog::with('user')->latest()->paginate(50);
        return view('hr.activity', compact('logs'));
    }
}
