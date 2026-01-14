<?php

namespace App\Service\Notifications;

use App\Models\User;

class NotificationService
{
    /**
     * جلب كافة الإشعارات (المقروءة وغير المقروءة)
     */
    public function getAllNotifications(User $user): array
    {
        return [
            'unread' => $user->unreadNotifications,
            'read'   => $user->readNotifications,
        ];
    }

    /**
     * جلب الإشعارات غير المقروءة فقط
     */
    public function getUnreadNotifications(User $user)
    {
        return $user->unreadNotifications;
    }

    /**
     * جلب الإشعارات المقروءة فقط
     */
    public function getReadNotifications(User $user)
    {
        return $user->readNotifications;
    }

    /**
     * تحديد إشعار واحد كـ "مقروء"
     */
    public function markAsRead(User $user, string $id)
    {
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            throw new \DomainException('NOTIFICATION_NOT_FOUND');
        }

        $notification->markAsRead();
        return $notification;
    }

    /**
     * تحديد كافة الإشعارات كـ "مقروءة"
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * حذف إشعار معين
     */
    public function deleteNotification(User $user, string $id): void
    {
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            throw new \DomainException('NOTIFICATION_NOT_FOUND');
        }

        $notification->delete();
    }
}