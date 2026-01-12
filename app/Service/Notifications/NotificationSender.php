<?php

namespace App\Service\Notifications;

use Illuminate\Notifications\Notification;
use App\Notifications\BookingNotifications;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationSender
{
    public function send(User $receiver, Notification $notification): void
    {
        $receiver->notify($notification);

        if (method_exists($notification, 'sendFcm')) {
            /** @var mixed $notification */
            $notification->sendFcm($receiver);
        }
    }
    
    // public function send(User $receiver, $notification): array
    // {
    //     $status = [
    //         'database' => false,
    //         'fcm' => false,
    //         'error' => null
    //     ];

    //     try {
    //         $receiver->notify($notification);
    //         $status['database'] = true;

    //         if (method_exists($notification, 'sendFcm')) {
    //             $fcmResult = $notification->sendFcm($receiver);
    //             $status['fcm'] = $fcmResult;
    //         }
            
    //     } catch (\Exception $e) {
    //         $status['error'] = $e->getMessage();
    //         Log::error('Notification System Error: ' . $e->getMessage());
    //     }

    //     return $status;
    // }
}
