<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Service\Notifications\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationsController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function showAllNotifications(): JsonResponse
    {
        $user = auth('user_api')->user();
        $notifications = $this->notificationService->getAllNotifications($user);

        return response()->json([
            'status' => true,
            'data'   => [
                'unread' => NotificationResource::collection($notifications['unread']),
                'read'   => NotificationResource::collection($notifications['read']),
            ],
            'code'   => 200
        ], 200);
    }

    public function unread(): JsonResponse
    {
        $user = auth('user_api')->user();
        $notifications = $this->notificationService->getUnreadNotifications($user);

        return response()->json([
            'status' => true,
            'data'   => NotificationResource::collection($notifications),
            'code'   => 200
        ], 200);
    }

    public function read(): JsonResponse
    {
        $user = auth('user_api')->user();
        $notifications = $this->notificationService->getReadNotifications($user);

        return response()->json([
            'status' => true,
            'data'   => NotificationResource::collection($notifications),
            'code'   => 200
        ], 200);
    }

    public function markAsRead(string $id): JsonResponse
    {
        $user = auth('user_api')->user();
        $this->notificationService->markAsRead($user, $id);

        return response()->json([
            'status'  => true,
            'message' => __('notifications.marked_as_read'),
            'code'    => 200
        ], 200);
    }

    public function markAllAsRead(): JsonResponse
    {
        $user = auth('user_api')->user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'status'  => true,
            'message' => __('notifications.marked_as_read'),
            'code'    => 200
        ], 200);
    }

    public function delete(string $id): JsonResponse
    {
        $user = auth('user_api')->user();
        $this->notificationService->deleteNotification($user, $id);

        return response()->json([
            'status'  => true,
            'message' => __('notifications.deleted'),
            'code'    => 200
        ], 200);
    }
}