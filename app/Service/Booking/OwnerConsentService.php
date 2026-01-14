<?php

namespace App\Service\Booking;



use App\Models\Booking;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Notifications\BookingNotifications;

use App\Service\Notifications\NotificationSender;



class OwnerConsentService

{

    public function __construct(

        protected NotificationSender $notificationSender

    ) {}



    public function approve(int $bookingId, $owner): Booking
    {

        return DB::transaction(function () use ($bookingId, $owner) {
            // قفل السطر للتأكد من عدم حدوث تغيير متزامن
            $booking = Booking::with('apartment')->lockForUpdate()->findOrFail($bookingId);

            if ($booking->apartment->user_id !== $owner->id) {
                throw new \DomainException('NOT_APARTMENT_OWNER');
            }

            // لا يمكن الموافقة إلا إذا كان الطلب معلقاً
            if ($booking->status !== 'pending') {
                throw new \DomainException('BOOKING_ALREADY_PROCESSED');
            }

            $booking->update(['status' => 'waiting_payment']);

            $notification = new BookingNotifications($booking, 'approved');
            $this->notificationSender->send($booking->user, $notification);

            return $booking;
        });
    }

    public function reject(int $bookingId, $owner): Booking

    {

        $booking = Booking::find($bookingId);

        if (!$booking) throw new ModelNotFoundException('BOOKING_NOT_FOUND');



        if ($booking->apartment->user_id !== $owner->id) {

            throw new \DomainException('NOT_APARTMENT_OWNER');
        }



        $booking->update(['status' => 'rejected']);

        $booking->payment?->update(['payment_status' => 'failed']);



        return $booking;
    }
}
