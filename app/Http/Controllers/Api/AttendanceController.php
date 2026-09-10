<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AppNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Dashboard - Today's status overview
     * MVP: Login → Dashboard
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Determine today's status
        $todayStatus = 'absent';
        if ($todayAttendance) {
            $todayStatus = $todayAttendance->status; // present, late, half-day
            // If checked_in but not checked_out, status is present
            if ($todayAttendance->check_in && !$todayAttendance->check_out) {
                $todayStatus = $todayAttendance->status;
            }
        } else {
            // Check if user is on leave today
            $onLeave = $user->leaves()
                ->where('status', 'approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->exists();
            if ($onLeave) {
                $todayStatus = 'leave';
            }
        }

        // Calculate working hours for today
        $workingHours = null;
        $workingHoursFormatted = null;
        if ($todayAttendance && $todayAttendance->check_in) {
            $checkIn = Carbon::parse($todayAttendance->check_in);
            $checkOut = $todayAttendance->check_out ? Carbon::parse($todayAttendance->check_out) : Carbon::now();
            $diffMinutes = $checkIn->diffInMinutes($checkOut);
            $hours = floor($diffMinutes / 60);
            $minutes = $diffMinutes % 60;
            $workingHoursFormatted = sprintf('%02d:%02d', $hours, $minutes);
            $workingHours = round($diffMinutes / 60, 2);
            // If already checked out, use stored total_hours
            if ($todayAttendance->total_hours && $todayAttendance->check_out) {
                $workingHours = (float) $todayAttendance->total_hours;
                $h = floor($workingHours);
                $m = round(($workingHours - $h) * 60);
                $workingHoursFormatted = sprintf('%02d:%02d', $h, $m);
            }
        }

        // Monthly stats for dashboard card
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();
        $monthAttendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get();

        $presentCount = $monthAttendances->whereIn('status', ['present', 'late'])->count();
        $absentCount = Carbon::now()->day - $presentCount - $user->leaves()->where('status','approved')->whereBetween('start_date', [$monthStart, $monthEnd])->count(); // approximate
        // More accurate absent: count weekdays without attendance? For MVP, just return present vs total days so far
        $totalDaysSoFar = Carbon::now()->day;
        $attendancePercentage = $totalDaysSoFar > 0 ? round(($presentCount / $totalDaysSoFar) * 100, 1) : 0;

        return response()->json([
            'today' => [
                'date' => $today,
                'status' => $todayStatus, // present | absent | late | leave
                'check_in' => $todayAttendance?->check_in,
                'check_out' => $todayAttendance?->check_out,
                'check_in_photo_url' => $todayAttendance?->check_in_photo_url,
                'check_out_photo_url' => $todayAttendance?->check_out_photo_url,
                'check_in_location' => $todayAttendance ? [
                    'latitude' => $todayAttendance->check_in_latitude,
                    'longitude' => $todayAttendance->check_in_longitude,
                    'address' => $todayAttendance->check_in_address,
                ] : null,
                'check_out_location' => $todayAttendance ? [
                    'latitude' => $todayAttendance->check_out_latitude,
                    'longitude' => $todayAttendance->check_out_longitude,
                    'address' => $todayAttendance->check_out_address,
                ] : null,
                'working_hours' => $workingHours,
                'working_hours_formatted' => $workingHoursFormatted,
                'can_check_in' => !$todayAttendance || !$todayAttendance->check_in,
                'can_check_out' => $todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out,
                'check_in_biometric_verified' => $todayAttendance?->check_in_biometric_verified ?? false,
                'check_out_biometric_verified' => $todayAttendance?->check_out_biometric_verified ?? false,
                'check_in_biometric_type' => $todayAttendance?->check_in_biometric_type,
                'check_out_biometric_type' => $todayAttendance?->check_out_biometric_type,
            ],
            'month_summary' => [
                'month' => Carbon::now()->format('Y-m'),
                'present' => $presentCount,
                'attendance_percentage' => $attendancePercentage,
                'total_days_so_far' => $totalDaysSoFar,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
                'position' => $user->position,
            ]
        ]);
    }

    /**
     * Get today's attendance only
     */
    public function today(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance) {
            return response()->json([
                'date' => $today,
                'status' => 'absent',
                'check_in' => null,
                'check_out' => null,
                'total_hours' => null,
                'message' => 'No attendance record for today',
            ]);
        }

        return response()->json($attendance);
    }

    /**
     * Check In
     * Required: latitude, longitude
     * Optional: photo (image), address, biometric_verified, biometric_type, device_info
     *
     * Biometric flow (recommended): Flutter does local_auth first, then sends
     *   biometric_verified=true + biometric_type=fingerprint|face + device_info
     * Backend does NOT store fingerprint template, only boolean+type+device.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            // Biometric – phone handles verification, backend only records success
            'biometric_verified' => 'nullable|boolean',
            'biometric_type' => 'nullable|string|in:fingerprint,face,iris,none',
            'device_info' => 'nullable|array',
            'device_info.platform' => 'nullable|string|max:50',
            'device_info.model' => 'nullable|string|max:100',
            'device_info.device_id' => 'nullable|string|max:255',
            'device_info.app_version' => 'nullable|string|max:50',
        ]);

        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Prevent double check-in
        $existing = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if ($existing && $existing->check_in) {
            return response()->json([
                'message' => 'Already checked in today',
                'attendance' => $existing,
            ], 422);
        }

        // Determine if late - compare with WorkShift start time (default 09:00)
        $status = 'present';
        try {
            $shiftStart = \App\Models\WorkShift::where('is_active', true)->first();
            $shiftStartTime = $shiftStart ? $shiftStart->start_time : '09:00:00';
            // $shiftStartTime may be like "09:00:00" or "09:00"
            $shiftStartCarbon = Carbon::createFromFormat('H:i:s', strlen($shiftStartTime) === 5 ? $shiftStartTime.':00' : $shiftStartTime);
            // If shiftStartCarbon is today
            $shiftStartToday = Carbon::today()->setTime($shiftStartCarbon->hour, $shiftStartCarbon->minute, $shiftStartCarbon->second);
            // Grace period 15 minutes?
            if ($now->greaterThan($shiftStartToday->copy()->addMinutes(15))) {
                $status = 'late';
            }
        } catch (\Throwable $e) {
            // fallback
            if ($now->hour > 9 || ($now->hour == 9 && $now->minute > 15)) {
                $status = 'late';
            }
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance/check-in', 'public');
        }

        // Check if on approved leave today -> cannot check in
        $onLeave = $user->leaves()
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();
        if ($onLeave) {
            return response()->json(['message' => 'You are on approved leave today. Cannot check in.'], 422);
        }

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'check_in' => $now,
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'check_in_address' => $request->address,
                'check_in_photo' => $photoPath,
                'status' => $status,
                'check_in_biometric_verified' => $request->boolean('biometric_verified'),
                'check_in_biometric_type' => $request->input('biometric_type'),
                'device_info' => $request->input('device_info'),
            ]
        );

        // Create notification for successful check-in
        $bioBadge = $request->boolean('biometric_verified') ? ' 🔐 Biometric verified' : '';
        AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Check-in Successful',
            'body' => 'You checked in at ' . $now->format('h:i A') . ($status === 'late' ? ' (Late)' : '') . $bioBadge,
            'type' => $status === 'late' ? 'late' : 'attendance',
            'data' => ['attendance_id' => $attendance->id, 'action' => 'check_in', 'biometric_verified' => $request->boolean('biometric_verified')],
        ]);

        return response()->json([
            'message' => $status === 'late' ? 'Checked in successfully (Late)' : 'Checked in successfully',
            'attendance' => $attendance,
        ], 201);
    }

    /**
     * Check Out
     * Required: latitude, longitude
     * Optional: photo (image), address, biometric_verified, biometric_type, device_info
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'biometric_verified' => 'nullable|boolean',
            'biometric_type' => 'nullable|string|in:fingerprint,face,iris,none',
            'device_info' => 'nullable|array',
            'device_info.platform' => 'nullable|string|max:50',
            'device_info.model' => 'nullable|string|max:100',
            'device_info.device_id' => 'nullable|string|max:255',
            'device_info.app_version' => 'nullable|string|max:50',
        ]);

        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json(['message' => 'You have not checked in today'], 422);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Already checked out today', 'attendance' => $attendance], 422);
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $diffMinutes = $checkIn->diffInMinutes($now);
        $totalHours = round($diffMinutes / 60, 2);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance/check-out', 'public');
        }

        $attendance->update([
            'check_out' => $now,
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'check_out_address' => $request->address,
            'check_out_photo' => $photoPath,
            'total_hours' => $totalHours,
            'check_out_biometric_verified' => $request->boolean('biometric_verified'),
            'check_out_biometric_type' => $request->input('biometric_type'),
            // merge device_info if provided (keep original check-in device_info if not overwritten)
            'device_info' => $request->input('device_info', $attendance->device_info),
        ]);

        // Notification
        AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Check-out Successful',
            'body' => 'You checked out at ' . $now->format('h:i A') . '. Worked ' . $attendance->working_hours_formatted,
            'type' => 'attendance',
            'data' => ['attendance_id' => $attendance->id, 'action' => 'check_out', 'hours' => $totalHours],
        ]);

        return response()->json([
            'message' => 'Checked out successfully',
            'attendance' => $attendance->fresh(),
        ]);
    }

    /**
     * Attendance History - Paginated with filters
     * Query: month (YYYY-MM), year, status, per_page, page
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
            'year' => 'nullable|digits:4|integer|min:2020|max:2030',
            'status' => 'nullable|in:present,absent,late,leave,half-day',
            'per_page' => 'nullable|integer|min:1|max:100',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $query = Attendance::where('user_id', $user->id)->orderBy('date', 'desc');

        if ($request->filled('month')) {
            $month = Carbon::createFromFormat('Y-m', $request->month);
            $query->whereYear('date', $month->year)->whereMonth('date', $month->month);
        } elseif ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('date', [$request->from, $request->to]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 15);
        $paginator = $query->paginate($perPage);

        // Append computed total_hours for records without checkout yet (today)
        $paginator->getCollection()->transform(function ($item) {
            if (!$item->total_hours && $item->check_in && !$item->check_out && $item->date->isToday()) {
                $item->total_hours = round(Carbon::parse($item->check_in)->diffInMinutes(Carbon::now()) / 60, 2);
                $item->working_hours_formatted = sprintf('%02d:%02d', floor($item->total_hours), round(($item->total_hours - floor($item->total_hours)) * 60));
            }
            return $item;
        });

        return response()->json($paginator);
    }

    /**
     * Calendar view - Returns month calendar data
     * Query: month=YYYY-MM (default current month)
     */
    public function calendar(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $month = $request->filled('month') ? Carbon::createFromFormat('Y-m', $request->month) : Carbon::now();
        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn($a) => $a->date->toDateString());

        $leaves = $user->leaves()
            ->where('status', 'approved')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_date', '<=', $start)->where('end_date', '>=', $end);
                  });
            })->get();

        // Build calendar array day by day
        $days = [];
        $cursor = $month->copy()->startOfMonth();
        $endCursor = $month->copy()->endOfMonth();

        $present = 0;
        $absent = 0;
        $late = 0;
        $leave = 0;

        while ($cursor->lte($endCursor)) {
            $dateStr = $cursor->toDateString();
            $attendance = $attendances->get($dateStr);

            // Check if on leave
            $isOnLeave = $leaves->first(function ($l) use ($dateStr) {
                return $dateStr >= $l->start_date->toDateString() && $dateStr <= $l->end_date->toDateString();
            });

            $status = null;
            if ($attendance) {
                $status = $attendance->status;
                if ($status === 'present') $present++;
                elseif ($status === 'late') { $late++; $present++; }
                elseif ($status === 'half-day') $present++;
            } elseif ($isOnLeave) {
                $status = 'leave';
                $leave++;
            } else {
                // Future dates are not counted as absent
                if ($cursor->isFuture()) {
                    $status = null;
                } else {
                    // Weekends? Consider sunday absent? For MVP count all past days without record as absent, but skip future
                    // If date is today and past today but no record -> absent
                    // Only count absent for past days (excluding today if not yet checked in? still absent for dashboard? For calendar, mark past empty as absent)
                    if ($cursor->isPast() || $cursor->isToday()) {
                        // Check if attendance expected? For now, mark past weekdays as absent if no leave
                        $status = 'absent';
                        if (!$cursor->isToday() || $cursor->hour > 18) {
                            // Only count past days (excluding today if early)
                        }
                        if ($cursor->lt(Carbon::today())) {
                            $absent++;
                        }
                    }
                }
            }

            $days[] = [
                'date' => $dateStr,
                'day' => $cursor->day,
                'weekday' => $cursor->format('D'),
                'status' => $status, // present | absent | late | leave | null (future)
                'check_in' => $attendance?->check_in?->format('H:i:s'),
                'check_out' => $attendance?->check_out?->format('H:i:s'),
                'total_hours' => $attendance?->total_hours,
                'is_weekend' => $cursor->isWeekend(),
                'is_today' => $cursor->isToday(),
                'is_future' => $cursor->isFuture(),
            ];

            $cursor->addDay();
        }

        $totalWorkingDays = $present + $absent + $late; // leave not counted as absent
        $attendancePercentage = $totalWorkingDays > 0 ? round(($present / $month->daysInMonth) * 100, 1) : 0;
        // Better: present / (days so far - leaves?) Let's give both
        $daysSoFar = min(Carbon::now()->day, $month->daysInMonth);
        if ($month->isFuture()) $daysSoFar = 0;
        if ($month->isPast()) $daysSoFar = $month->daysInMonth;
        $percentageSoFar = $daysSoFar > 0 ? round(($present / $daysSoFar) * 100, 1) : 0;

        return response()->json([
            'month' => $month->format('Y-m'),
            'month_name' => $month->format('F Y'),
            'days' => $days,
            'summary' => [
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'leave' => $leave,
                'total_days_in_month' => $month->daysInMonth,
                'attendance_percentage' => $attendancePercentage,
                'attendance_percentage_so_far' => $percentageSoFar,
            ]
        ]);
    }

    /**
     * Stats overview
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
            'year' => 'nullable|digits:4',
        ]);

        $month = $request->filled('month') ? Carbon::createFromFormat('Y-m', $request->month) : Carbon::now();
        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->get();

        $present = $attendances->where('status', 'present')->count();
        $late = $attendances->where('status', 'late')->count();
        $halfDay = $attendances->where('status', 'half-day')->count();
        $totalPresent = $present + $late + $halfDay;

        $leavesApproved = $user->leaves()->where('status','approved')->whereBetween('start_date', [$start,$end])->count();

        $totalHours = $attendances->sum('total_hours');
        $avgHours = $attendances->whereNotNull('total_hours')->count() > 0 ? round($totalHours / $attendances->whereNotNull('total_hours')->count(), 2) : 0;

        $daysInMonth = $month->daysInMonth;
        $daysSoFar = $month->isCurrentMonth() ? Carbon::now()->day : ($month->isPast() ? $daysInMonth : 0);
        $percentage = $daysSoFar > 0 ? round(($totalPresent / $daysSoFar) * 100, 1) : 0;

        return response()->json([
            'month' => $month->format('Y-m'),
            'month_name' => $month->format('F Y'),
            'present' => $present,
            'late' => $late,
            'half_day' => $halfDay,
            'total_present' => $totalPresent,
            'absent' => max(0, $daysSoFar - $totalPresent - $leavesApproved),
            'leave' => $leavesApproved,
            'total_hours' => round($totalHours, 2),
            'avg_hours_per_day' => $avgHours,
            'attendance_percentage' => $percentage,
            'days_in_month' => $daysInMonth,
            'days_so_far' => $daysSoFar,
        ]);
    }
}
