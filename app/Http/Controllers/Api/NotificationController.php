<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NotificationController extends Controller
{
    /**
     * List user notifications with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $user    = $request->user();
        $perPage = (int) $request->query('per_page', 10);
        $notifications = $user->notifications()->paginate($perPage);
        return Response::success(NotificationResource::collection($notifications)->response()->getData(true), 'Notifications retrieved successfully');
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();
        if (!$notification) {
            return Response::notFound('Notification not found');
        }
        $notification->markAsRead();
        return Response::success(new NotificationResource($notification), 'Notification marked as read');
    }

    /**
     * Mark all notifications as read for the user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        return Response::success(null, 'All notifications marked as read');
    }
}
