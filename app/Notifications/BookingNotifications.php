<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Support\Facades\Log;

class BookingNotifications extends Notification
{
    use Queueable;

    /**
     * Booking instance
     * Status: approved | rejected | cancelled | update_request
     */
    public function __construct(
        protected Booking $booking,
        protected string $status
    ) {}

    /**
     * Delivery channels
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Database representation
     */
    public function toDatabase($notifiable): array
    {
        $data = $this->getNotificationData($notifiable);
        return array_merge($data, [
            'booking_id' => $this->booking->id,
            'apartment_id' => $this->booking->apartment_id,
        ]);
    }

    /**
     * Send FCM notification
     */
    public function sendFcm($notifiable): void
    {
        if (!$notifiable->fcm_token)
             throw new \Exception('NO_FCM_TOKEN');

        $data = $this->getNotificationData($notifiable);

        try {
            Firebase::messaging()->send([
                'token' => $notifiable->fcm_token,
                'notification' => [
                    'title' => $data['title'],
                    'body'  => $data['body'],
                ],
                'data' => [
                    'type' => $data['type'],
                    'action' => $data['action'],
                    'booking_id' => (string)$this->booking->id,
                    'apartment_id' => (string)$this->booking->apartment_id,
                    'user_role' => $data['user_role'],
                ],
            ]);
        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            $notifiable->update(['fcm_token' => null]);
        } catch (\Exception $e) {
            Log::error('FCM notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Get notification data based on status and notifiable
     */
    private function getNotificationData($notifiable): array
    {
        $isOwner = $notifiable->id === $this->booking->apartment->user_id;

        return match ($this->status) {
            'pending' => [
                'title' => __('notifications.new_booking_title'),
                'body' => __('notifications.new_booking_body'),
                'type' => 'booking',
                'action' => 'new',
                'user_role' => 'owner',
            ],
            'approved' => [
                'title' => __('notifications.booking_approved_title'),
                'body' => __('notifications.booking_approved_body'),
                'type' => 'booking',
                'action' => 'approve',
                'user_role' => 'tenant',
            ],
            'rejected' => [
                'title' => __('notifications.booking_rejected_title'),
                'body' => __('notifications.booking_rejected_body'),
                'type' => 'booking',
                'action' => 'reject',
                'user_role' => 'tenant',
            ],
            'cancelled' => [
                'title' => __('notifications.booking_cancelled_title'),
                'body' => __('notifications.booking_cancelled_body'),
                'type' => 'booking',
                'action' => 'cancel',
                'user_role' => $isOwner ? 'owner' : 'tenant',
            ],
            'update_request' => [
                'title' => __('notifications.update_request_title'),
                'body' => __('notifications.update_request_body'),
                'type' => 'booking',
                'action' => 'update_request',
                'user_role' => 'owner',
            ],
            'update_approved' => [
                'title' => __('notifications.update_approved_title'),
                'body' => __('notifications.update_approved_body'),
                'type' => 'booking',
                'action' => 'update_approved',
                'user_role' => 'tenant',
            ],
            'update_rejected' => [
                'title' => __('notifications.update_rejected_title'),
                'body' => __('notifications.update_rejected_body'),
                'type' => 'booking',
                'action' => 'update_rejected',
                'user_role' => 'tenant',
            ],
            'payment_failed_booking_cancelled' => [
                'title' => __('notifications.payment_failed_title'),
                'body' => __('notifications.payment_failed_body'),
                'type' => 'payment',
                'action' => 'failed',
                'user_role' => 'tenant',
            ],
            default => [
                'title' => __('notifications.general_title'),
                'body' => __('notifications.general_body'),
                'type' => 'general',
                'action' => 'general',
                'user_role' => 'user',
            ],
        };
    }
}
