<?php

namespace App\Service\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function getAllNotifications(User $user)
    {
        return [
            'unread' => $user->unreadNotifications,
            'read' => $user->readNotifications,
        ];
    }

    public function getUnreadNotifications(User $user)
    {
        return $user->unreadNotifications;
    }

    public function getReadNotifications(User $user)
    {
        return $user->readNotifications;
    }

    public function markAsRead(User $user, string $id)
    {
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            throw new \Exception(__('notifications.notification_not_found'));
        }

        $notification->markAsRead();

        return $notification;
    }

    public function markAllAsRead(User $user)
    {
        $user->unreadNotifications->markAsRead();

        return true;
    }

    public function deleteNotification($user, string $id)
    {
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            throw new \Exception(__('notifications.notification_not_found'));
        }

        $notification->delete();

        return true;
    }
}
