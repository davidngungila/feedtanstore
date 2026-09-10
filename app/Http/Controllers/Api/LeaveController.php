<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /**
     * List leaves for authenticated user - with filter by status
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending,approved,rejected,cancelled',
            'leave_type' => 'nullable|in:sick,casual,annual,emergency,maternity,paternity,other',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = $request->user()->leaves()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        $perPage = $request->input('per_page', 15);
        $leaves = $query->paginate($perPage);

        // Summary
        $summary = [
            'pending' => $request->user()->leaves()->where('status','pending')->count(),
            'approved' => $request->user()->leaves()->where('status','approved')->count(),
            'rejected' => $request->user()->leaves()->where('status','rejected')->count(),
            'total' => $request->user()->leaves()->count(),
        ];

        return response()->json([
            'data' => $leaves->items(),
            'meta' => [
                'current_page' => $leaves->currentPage(),
                'last_page' => $leaves->lastPage(),
                'per_page' => $leaves->perPage(),
                'total' => $leaves->total(),
            ],
            'summary' => $summary,
            'links' => [
                'first' => $leaves->url(1),
                'last' => $leaves->url($leaves->lastPage()),
                'prev' => $leaves->previousPageUrl(),
                'next' => $leaves->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Apply for leave
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:sick,casual,annual,emergency,maternity,paternity,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:5|max:1000',
        ]);

        // Prevent overlapping pending/approved leaves
        $overlap = $request->user()->leaves()
            ->whereIn('status', ['pending','approved'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            })->exists();

        if ($overlap) {
            return response()->json(['message' => 'You already have a leave request overlapping these dates'], 422);
        }

        $leave = Leave::create([
            'user_id' => $request->user()->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Notify user
        AppNotification::create([
            'user_id' => $request->user()->id,
            'title' => 'Leave Request Submitted',
            'body' => 'Your '.$leave->leave_type.' leave from '.$leave->start_date->format('Y-m-d').' to '.$leave->end_date->format('Y-m-d').' is pending approval.',
            'type' => 'leave',
            'data' => ['leave_id' => $leave->id, 'action' => 'applied'],
        ]);

        return response()->json([
            'message' => 'Leave application submitted successfully',
            'leave' => $leave,
        ], 201);
    }

    /**
     * Show single leave
     */
    public function show(Request $request, $id)
    {
        $leave = $request->user()->leaves()->with('reviewer:id,name,email')->findOrFail($id);
        return response()->json($leave);
    }

    /**
     * Cancel pending leave
     */
    public function cancel(Request $request, $id)
    {
        $leave = $request->user()->leaves()->findOrFail($id);

        if ($leave->status !== 'pending') {
            return response()->json(['message' => 'Only pending leaves can be cancelled'], 422);
        }

        $leave->update(['status' => 'cancelled']);

        AppNotification::create([
            'user_id' => $request->user()->id,
            'title' => 'Leave Cancelled',
            'body' => 'Your leave from '.$leave->start_date->format('Y-m-d').' to '.$leave->end_date->format('Y-m-d').' has been cancelled.',
            'type' => 'leave',
            'data' => ['leave_id' => $leave->id, 'action' => 'cancelled'],
        ]);

        return response()->json(['message' => 'Leave cancelled successfully', 'leave' => $leave]);
    }

    /**
     * Admin: Approve/Reject leave (for web admin or manager role)
     * Optional - expose for admin app
     */
    public function review(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_notes' => 'nullable|string|max:1000',
        ]);

        // Check if reviewer has permission (admin/manager) - for now allow any authenticated user to review if they are not the applicant? Better restrict to admin role
        // If using this in mobile for managers, keep simple. Otherwise remove.
        $leave = Leave::findOrFail($id);

        if ($leave->user_id === $request->user()->id) {
            return response()->json(['message' => 'You cannot review your own leave'], 403);
        }

        $leave->update([
            'status' => $request->status,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);

        // Notify applicant
        AppNotification::create([
            'user_id' => $leave->user_id,
            'title' => $request->status === 'approved' ? 'Leave Approved' : 'Leave Rejected',
            'body' => 'Your '.$leave->leave_type.' leave from '.$leave->start_date->format('Y-m-d').' to '.$leave->end_date->format('Y-m-d').' has been '.$request->status.'.' . ($request->review_notes ? ' Note: '.$request->review_notes : ''),
            'type' => 'leave',
            'data' => ['leave_id' => $leave->id, 'action' => $request->status],
        ]);

        return response()->json(['message' => 'Leave '.$request->status.' successfully', 'leave' => $leave]);
    }
}
