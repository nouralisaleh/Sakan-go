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
        return [
            'booking_id' => $this->booking->id,
            'apartment_id' => $this->booking->apartment_id,
            'status'     => $this->status,
            'message'    => $this->message($notifiable),
        ];
    }

    /**
     * Send FCM notification
     */
    public function sendFcm($notifiable): void
    {
        if (!$notifiable->fcm_token) 
             throw new \Exception('NO_FCM_TOKEN');

        try {
            Firebase::messaging()->send([
                'token' => $notifiable->fcm_token,
                'notification' => [
                    'title' => __('notifications.booking_title'),
                    'body'  => $this->message($notifiable),
                ],
                'data' => [
                    'booking_id' => (string)$this->booking->id,
                    'status'     => $this->status,
                ],
            ]);
        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            $notifiable->update(['fcm_token' => null]);
        } catch (\Exception $e) {
            Log::error('FCM notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Localized message
     */
    
    private function message($notifiable): string
    {
        return match ($this->status) {
            'pending'        => __('notifications.pending'),
            'approved'       => __('notifications.approved'),
            'rejected'       => __('notifications.rejected'),
            'cancelled'      => __('notifications.cancelled'),
            'update_request' => __('notifications.update_request'),
        };
    }
}
