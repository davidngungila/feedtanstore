<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    /**
     * List notifications - paginated, unread first
     */
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:attendance,leave,late,reminder,general,system',
            'is_read' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = $request->user()->appNotifications()->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        $perPage = $request->input('per_page', 20);
        $notifications = $query->paginate($perPage);

        return response()->json($notifications);
    }

    /**
     * Unread count
     */
    public function unreadCount(Request $request)
    {
        $count = $request->user()->appNotifications()->where('is_read', false)->count();
        return response()->json(['unread_count' => $count]);
    }

    /**
     * Mark single as read
     */
    public function markRead(Request $request, $id)
    {
        $notification = $request->user()->appNotifications()->findOrFail($id);
        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'Notification marked as read', 'notification' => $notification]);
    }

    /**
     * Mark all as read
     */
    public function markAllRead(Request $request)
    {
        $request->user()->appNotifications()->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id)
    {
        $notification = $request->user()->appNotifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Clear all read notifications
     */
    public function clearRead(Request $request)
    {
        $deleted = $request->user()->appNotifications()->where('is_read', true)->delete();
        return response()->json(['message' => "$deleted read notifications cleared"]);
    }
}
