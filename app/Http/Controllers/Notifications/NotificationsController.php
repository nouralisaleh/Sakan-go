<?php

namespace App\Http\Controllers\Notifications;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function showAllNotifications()
    {
      $user=auth('user_api')->user();
        return response()->json([
            'unread' =>NotificationResource::collection($user->unreadNotifications),
            'read'   =>NotificationResource::collection($user->readNotifications),
        ]);
    }

    public function unread()
    {
        return new NotificationResource(auth('user_api')->user()->unreadNotifications);
    }
    public function read()
    {
        return new NotificationResource(auth('user_api')->user()->readNotifications);
    }

    public function markAsRead(string $id)
    {
        $notification = auth('user_api')->user()->notifications->where('id', $id)->first();

        $notification->markAsRead();

        return response()->json(['message' => __('notifications.marked_as_read')]);
    }

    public function markAllAsRead()
    {
        auth('user_api')->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => __('notifications.marked_as_read')]);
    }

    public function delete(string $id,)
    {
       
       $notification=auth('user_api')->user()
            ->notifications
            ->where('id', $id)
            ->delete();    

        return response()->json(['message' => __('notifications.deleted')]);
    }
}
