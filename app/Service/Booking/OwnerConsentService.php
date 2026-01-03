<?php
namespace App\Service\Booking;

use App\Models\Apartment;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OwnerConsentService{
    public function reject($booking_id,$user)
       {
        $booking= Booking::find($booking_id);
        if (!$booking)
            {
                throw new ModelNotFoundException('BOOKING_NOT_FOUND');
            }
        if($booking->apartment->user_id !== $user->id)
        {
            throw new \DomainException('FORBIDDEN_ACTION');
        }
        if (in_array($booking->status, ['rejected', 'cancelled']))
            {
                throw new \DomainException('BOOKING_ALREADY_FINALIZED');
            }

            $booking->update([
                'status' => 'rejected',
            ]);

            return $booking;
       }
 public function approve(int $bookingId, $user)
{
    return DB::transaction(function () use ($bookingId, $user) {

        $booking = Booking::lockForUpdate()->find($bookingId);

        if (!$booking) {
            throw new ModelNotFoundException('BOOKING_NOT_FOUND');
        }
        if($booking->apartment->user_id !== $user->id)
        {
            throw new \DomainException('FORBIDDEN_ACTION');
        }

        if ($booking->status !== 'pending') {
            throw new \DomainException('BOOKING_ALREADY_FINALIZED');
        }

        Booking::where('apartment_id', $booking->apartment_id)
            ->where('id', '!=', $booking->id)
            ->where('status', 'pending')
            ->where(function ($q) use ($booking) {
                $q->where('start_date', '<=', $booking->end_date)
                  ->where('end_date', '>=', $booking->start_date);
            })
            ->update(['status' => 'rejected']);

        $booking->update(['status' => 'confirmed']);

        return $booking;
    });
}

    
}