<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\WorkShift;
use App\Models\ActionLog;
use App\Models\CommunicationProfile;
use App\Support\Permissions;
use App\Mail\EmployeeWelcomeEmail;
use App\Services\MessagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HRController extends Controller
{
    protected function ensure(string $action): void
    {
        if (!Permissions::allows(Auth::user()->role, 'hr', $action)) {
            abort(403, "Unauthorized. You do not have the '{$action}' permission for HR.");
        }
    }

    protected function log(string $action, string $details): void
    {
        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Employees
    public function employees(Request $request)
    {
        $this->ensure('read');

        $q = trim((string) $request->input('q'));
        $roleFilter = (string) $request->input('role');

        $employees = User::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")))
            ->when($roleFilter !== '', fn ($query) => $query->where('role', $roleFilter))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('hr.employees', [
            'employees' => $employees,
            'roles' => Permissions::ROLES,
            'q' => $q,
            'roleFilter' => $roleFilter,
            'canManage' => Permissions::allows(Auth::user()->role, 'hr', 'update'),
            'canDelete' => Permissions::allows(Auth::user()->role, 'hr', 'delete'),
        ]);
    }

    public function createEmployee()
    {
        $this->ensure('create');
        return view('hr.employees-create', ['roles' => Permissions::ROLES]);
    }

    public function storeEmployee(Request $request)
    {
        $this->ensure('create');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'role' => 'required|in:' . implode(',', Permissions::ROLES),
        ]);

        $generatedPassword = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($generatedPassword),
            'role' => $request->role,
        ]);

        $notified = $this->sendWelcomeCredentials($user, $generatedPassword);

        $this->log('create_employee', 'Created new employee: ' . $user->name . ' (' . $user->role . ')');

        return redirect()->route('hr.employees')->with(
            'success',
            'Employee created successfully! ' . $notified
        );
    }

    protected function sendWelcomeCredentials(User $user, string $password): string
    {
        $emailed = false;
        $smsed = false;
        $emailError = null;

        try {
            $emailProfile = CommunicationProfile::where('type', 'email')->where('is_active', true)->first();
            $mailer = config('mail.default');
            if ($emailProfile && $emailProfile->smtp_host && $emailProfile->smtp_username && $emailProfile->smtp_password) {
                config([
                    'mail.mailers.test_smtp' => [
                        'transport' => 'smtp',
                        'host' => $emailProfile->smtp_host,
                        'port' => $emailProfile->smtp_port ?: 587,
                        'encryption' => $emailProfile->smtp_encryption,
                        'username' => $emailProfile->smtp_username,
                        'password' => $emailProfile->smtp_password,
                        'timeout' => 30,
                        'local_domain' => null,
                    ],
                ]);
                if ($emailProfile->email_from_address) {
                    config([
                        'mail.from.address' => $emailProfile->email_from_address,
                        'mail.from.name' => $emailProfile->email_from_name ?? config('app.name'),
                    ]);
                }
                $mailer = 'test_smtp';
            } elseif ($emailProfile) {
                Log::warning('Email communication profile incomplete, using default mailer', [
                    'profile_id' => $emailProfile->id,
                    'has_host' => (bool) $emailProfile->smtp_host,
                    'has_username' => (bool) $emailProfile->smtp_username,
                    'has_password' => (bool) $emailProfile->smtp_password,
                ]);
            }
            Mail::mailer($mailer)->to($user->email)->send(new EmployeeWelcomeEmail($user, $password));
            $emailed = true;
        } catch (\Throwable $e) {
            $emailError = $e->getMessage();
            Log::error('Failed to send welcome email to employee', [
                'employee_id' => $user->id,
                'email' => $user->email,
                'error' => $emailError,
            ]);
        }

        if ($user->phone) {
            try {
                $roleLabel = ucfirst(str_replace('_', ' ', $user->role));
                $settings = \App\Models\StoreSetting::firstOrCreate();
                $loginUrl = rtrim($settings->store_url ?? config('app.url'), '/') . '/login';
                $smsText = "Welcome to Feedtan Store, {$user->name}! Your {$roleLabel} account is ready. "
                    . "Login at {$loginUrl} | Email: {$user->email} | Password: {$password}. "
                    . 'Please change your password after first login.';
                $result = (new MessagingService())->sendSms($user->phone, $smsText);
                $smsed = (bool) ($result['success'] ?? false);
                if (!$smsed) {
                    Log::warning('Welcome SMS to employee not sent', [
                        'employee_id' => $user->id,
                        'response' => $result['response'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send welcome SMS to employee', [
                    'employee_id' => $user->id,
                    'phone' => $user->phone,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $reason = $emailError ? ' Reason: ' . Str::limit($emailError, 160) : '';

        if ($emailed && $smsed) {
            return 'An auto-generated password has been emailed and sent by SMS.';
        }
        if ($emailed) {
            return 'An auto-generated password has been emailed' . ($user->phone ? ' (SMS delivery failed)' : '') . '.';
        }
        if ($smsed) {
            return 'An auto-generated password has been sent by SMS. Email delivery failed.' . $reason;
        }

        return 'Warning: login credentials could not be delivered automatically — please share them manually.' . $reason;
    }

    public function editEmployee($id)
    {
        $this->ensure('update');
        $employee = User::findOrFail($id);
        return view('hr.employees-edit', ['employee' => $employee, 'roles' => Permissions::ROLES]);
    }

    public function updateEmployee(Request $request, $id)
    {
        $this->ensure('update');

        $employee = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,
            'role' => 'required|in:' . implode(',', Permissions::ROLES),
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

        $this->log('update_employee', 'Updated employee: ' . $employee->name);

        return redirect()->route('hr.employees')->with('success', 'Employee updated successfully!');
    }

    public function deleteEmployee($id)
    {
        $this->ensure('delete');

        if ((int) $id === (int) Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $employee = User::findOrFail($id);
        $employeeName = $employee->name;
        $employee->delete();

        $this->log('delete_employee', 'Deleted employee: ' . $employeeName);

        return redirect()->route('hr.employees')->with('success', 'Employee deleted successfully!');
    }

    // Roles & Permissions
    public function roles()
    {
        $this->ensure('read');

        return view('hr.roles', [
            'roles' => Permissions::ROLES,
            'modules' => Permissions::MODULES,
            'actions' => Permissions::ACTIONS,
            'matrix' => Permissions::matrix(),
            'canManage' => Permissions::allows(Auth::user()->role, 'hr', 'update'),
        ]);
    }

    public function saveRoles(Request $request)
    {
        $this->ensure('update');

        Permissions::sync($request->input('perm', []));

        $this->log('update_permissions', 'Updated role permissions matrix');

        return redirect()->route('hr.roles')->with('success', 'Permissions updated successfully!');
    }

    // Attendance
    public function attendance(Request $request)
    {
        $this->ensure('read');

        $date = (string) $request->input('date');
        $status = (string) $request->input('status');
        $userId = (string) $request->input('user_id');
        $today = today()->toDateString();

        $attendances = Attendance::with('user')
            ->when($date !== '', fn ($q) => $q->whereDate('date', $date))
            ->when(in_array($status, ['present', 'absent', 'late', 'half-day']), fn ($q) => $q->where('status', $status))
            ->when($userId !== '', fn ($q) => $q->where('user_id', $userId))
            ->orderByDesc('date')
            ->orderBy('user_id')
            ->paginate(30)
            ->withQueryString();

        $todayAttendance = Attendance::where('user_id', Auth::id())->where('date', $today)->first();
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('hr.attendance', [
            'attendances' => $attendances,
            'todayAttendance' => $todayAttendance,
            'users' => $users,
            'filters' => ['date' => $date, 'status' => $status, 'user_id' => $userId],
            'canManage' => Permissions::allows(Auth::user()->role, 'hr', 'update'),
            'canDelete' => Permissions::allows(Auth::user()->role, 'hr', 'delete'),
        ]);
    }

    public function storeAttendance(Request $request)
    {
        $this->ensure('update');

        $data = $this->validateAttendance($request);

        $exists = Attendance::where('user_id', $data['user_id'])->whereDate('date', $data['date'])->exists();
        if ($exists) {
            return back()->with('error', 'An attendance record already exists for that employee on that date.');
        }

        Attendance::create($data);

        $employee = User::find($data['user_id']);
        $this->log('create_attendance', 'Added attendance record for ' . ($employee->name ?? 'employee') . ' on ' . $data['date'] . ' (' . $data['status'] . ')');

        return back()->with('success', 'Attendance record added successfully!');
    }

    public function updateAttendance(Request $request, $id)
    {
        $this->ensure('update');

        $attendance = Attendance::findOrFail($id);
        $data = $this->validateAttendance($request);

        $duplicate = Attendance::where('user_id', $data['user_id'])
            ->whereDate('date', $data['date'])
            ->where('id', '!=', $attendance->id)
            ->exists();
        if ($duplicate) {
            return back()->with('error', 'Another attendance record already exists for that employee on that date.');
        }

        $attendance->update($data);

        $this->log('update_attendance', 'Updated attendance record #' . $attendance->id . ' (' . $data['status'] . ' on ' . $data['date'] . ')');

        return back()->with('success', 'Attendance record updated successfully!');
    }

    public function destroyAttendance($id)
    {
        $this->ensure('delete');

        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        $this->log('delete_attendance', 'Deleted attendance record #' . $id);

        return back()->with('success', 'Attendance record deleted successfully!');
    }

    protected function validateAttendance(Request $request): array
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,half-day',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['check_in'] = !empty($validated['check_in']) ? Carbon::parse($validated['date'] . ' ' . $validated['check_in']) : null;
        $validated['check_out'] = !empty($validated['check_out']) ? Carbon::parse($validated['date'] . ' ' . $validated['check_out']) : null;

        return $validated;
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

        $this->log('check_in', Auth::user()->name . ' checked in');

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

        $this->log('check_out', Auth::user()->name . ' checked out');

        return back()->with('success', 'Checked out successfully!');
    }

    // Work Shifts
    public function shifts()
    {
        $this->ensure('read');

        $shifts = WorkShift::orderBy('start_time')->paginate(20);

        return view('hr.shifts', [
            'shifts' => $shifts,
            'canManage' => Permissions::allows(Auth::user()->role, 'hr', 'update'),
            'canDelete' => Permissions::allows(Auth::user()->role, 'hr', 'delete'),
        ]);
    }

    public function createShift()
    {
        $this->ensure('create');
        return view('hr.shifts-create');
    }

    public function storeShift(Request $request)
    {
        $this->ensure('create');

        $validated = $this->validateShift($request);

        WorkShift::create($validated + ['is_active' => $request->boolean('is_active')]);

        $this->log('create_shift', 'Created shift: ' . $validated['name'] . ' (' . $validated['start_time'] . ' - ' . $validated['end_time'] . ')');

        return redirect()->route('hr.shifts')->with('success', 'Shift created successfully!');
    }

    public function editShift($id)
    {
        $this->ensure('update');
        $shift = WorkShift::findOrFail($id);
        return view('hr.shifts-edit', compact('shift'));
    }

    public function updateShift(Request $request, $id)
    {
        $this->ensure('update');

        $shift = WorkShift::findOrFail($id);
        $validated = $this->validateShift($request);

        $shift->update($validated + ['is_active' => $request->boolean('is_active')]);

        $this->log('update_shift', 'Updated shift: ' . $shift->name);

        return redirect()->route('hr.shifts')->with('success', 'Shift updated successfully!');
    }

    protected function validateShift(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'description' => 'nullable|string|max:1000',
        ]);
    }

    public function toggleShift($id)
    {
        $this->ensure('update');

        $shift = WorkShift::findOrFail($id);
        $shift->update(['is_active' => !$shift->is_active]);

        $state = $shift->is_active ? 'activated' : 'deactivated';
        $this->log('toggle_shift', "{$state} shift: {$shift->name}");

        return back()->with('success', "Shift {$state}!");
    }

    public function deleteShift($id)
    {
        $this->ensure('delete');

        $shift = WorkShift::findOrFail($id);
        $shiftName = $shift->name;
        $shift->delete();

        $this->log('delete_shift', 'Deleted shift: ' . $shiftName);

        return back()->with('success', 'Shift deleted successfully!');
    }

    // Activity Logs
    public function activity(Request $request)
    {
        $this->ensure('read');

        $q = trim((string) $request->input('q'));
        $userId = (string) $request->input('user_id');
        $from = (string) $request->input('from');
        $to = (string) $request->input('to');

        $logs = ActionLog::with('user')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('action', 'like', "%{$q}%")
                ->orWhere('details', 'like', "%{$q}%")))
            ->when($userId !== '', fn ($query) => $query->where('user_id', $userId))
            ->when($from !== '', fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to !== '', fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name']);

        return view('hr.activity', [
            'logs' => $logs,
            'users' => $users,
            'filters' => ['q' => $q, 'user_id' => $userId, 'from' => $from, 'to' => $to],
        ]);
    }
}
