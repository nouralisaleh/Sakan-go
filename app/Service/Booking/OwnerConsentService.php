<?php
namespace App\Service\Booking;

use App\Models\Apartment;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OwnerConsentService{
    public function reject($booking_id)
       {
        $booking= Booking::find($booking_id);
        if (!$booking)
            {
                throw new ModelNotFoundException('BOOKING_NOT_FOUND');
            }
        if (in_array($booking->status, ['rejected', 'cancelled']))
            {
                throw new \DomainException('BOOKING_ALREADY_FINALIZED');
            }

            $booking->update([
                'status' => 'cancelled',
            ]);

            return $booking;
       }

    
}