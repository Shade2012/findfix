<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     * GET /api/notifications
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(15);

        return response()->success(
            $notifications,
            'Notifications retrieved successfully',
            Response::HTTP_OK
        );
    }

    /**
     * Get unread notification count
     * GET /api/notifications/unread-count
     */
    public function unreadCount(Request $request)
    {
        $count = $request->user()
            ->unreadNotifications()
            ->count();

        return response()->success(
            ['count' => $count],
            'Unread count retrieved',
            Response::HTTP_OK
        );
    }

    /**
     * Mark a single notification as read
     * POST /api/notifications/{id}/read
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->error(
                'Notification not found',
                null,
                Response::HTTP_NOT_FOUND
            );
        }

        $notification->markAsRead();

        return response()->success(
            null,
            'Notification marked as read',
            Response::HTTP_OK
        );
    }

    /**
     * Mark all notifications as read
     * POST /api/notifications/read-all
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return response()->success(
            null,
            'All notifications marked as read',
            Response::HTTP_OK
        );
    }
}
