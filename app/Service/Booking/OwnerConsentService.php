<?php

namespace App\Service\Booking;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Notifications\BookingNotifications;
use App\Service\Notifications\NotificationSender;

class OwnerConsentService
{
    protected NotificationSender $notificationSender;

    public function __construct(NotificationSender $notificationSender)
    {
        $this->notificationSender = $notificationSender;
    }

    public function approve(int $bookingId, $owner)
    {
        return DB::transaction(function () use ($bookingId, $owner) {

            $booking = Booking::lockForUpdate()->find($bookingId);
            if (!$booking) {
                throw new ModelNotFoundException('BOOKING_NOT_FOUND');
            }

            if ($booking->apartment->user_id !== $owner->id) {
                throw new \DomainException('NOT_APARTMENT_OWNER');
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

            $payment = $booking->payment;
            if (!$payment || $payment->payment_status !== 'pending') {
                throw new \DomainException('INVALID_PAYMENT_STATE');
            }

            /** ======================
             *  محاكاة الدفع
             *  ======================
             */
            if ($payment->payment_method === 'wallet') {
                $wallet = $booking->user->wallet;

                if (!$wallet || $wallet->balance < $payment->amount) {
                    throw new \DomainException('INSUFFICIENT_WALLET_BALANCE');
                }

               $wallet->decrement('balance', $payment->amount);

            } elseif ($payment->payment_method === 'credit_card') {
                // محاكاة نجاح بطاقة
                // لا شي حقيقي
            }

            $payment->update([
                'payment_status' => 'completed',
            ]);

            $booking->update([
                'status' => 'confirmed',
            ]);

            $notification = new BookingNotifications($booking, 'approved');
            $this->notificationSender->send($booking->user, $notification);

            return $booking;
        });
    }

    public function reject(int $bookingId, $owner)
    {
        $booking = Booking::find($bookingId);
        if (!$booking) {
            throw new ModelNotFoundException('BOOKING_NOT_FOUND');
        }

        if ($booking->apartment->user_id !== $owner->id) {
            throw new \DomainException('NOT_APARTMENT_OWNER');
        }

        $booking->update(['status' => 'rejected']);

        if ($booking->payment) {
            $booking->payment->update([
                'payment_status' => 'failed'
            ]);
        }

        $notification = new BookingNotifications($booking, 'rejected');
        $this->notificationSender->send($booking->user, $notification);

        return $booking;
    }
}
