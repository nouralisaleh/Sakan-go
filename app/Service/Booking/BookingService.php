<?php

namespace App\Service\Booking;



use App\Models\Apartment;

use App\Models\Booking;

use App\Models\Payment;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Notifications\BookingNotifications;

use App\Service\Notifications\NotificationSender;
use Carbon\Carbon;




class BookingService

{

    public function __construct(

        protected NotificationSender $notificationSender

    ) {}



    public function store(int $userId, int $apartmentId, array $data): Booking

    {

        $apartment = Apartment::find($apartmentId);

        if (!$apartment) {

            throw new ModelNotFoundException('APARTMENT_NOT_FOUND');

        }



        return DB::transaction(function () use ($userId, $apartment, $data) {



            $conflict = Booking::where('apartment_id', $apartment->id)

                ->whereIn('status', ['pending', 'waiting_payment', 'confirmed'])

                ->where(function ($q) use ($data) {

                    $q->where('start_date', '<=', $data['end_date'])

                      ->where('end_date', '>=', $data['start_date']);

                })

                ->lockForUpdate()

                ->exists();



            if ($conflict) {

                throw new \DomainException('BOOKING_CONFLICT');

            }



            $days = now()->parse($data['start_date'])

                ->diffInDays(now()->parse($data['end_date']));



            $totalPrice = $days * $apartment->price;



            $booking = Booking::create([

                'user_id'      => $userId,

                'apartment_id' => $apartment->id,

                'start_date'   => $data['start_date'],

                'end_date'     => $data['end_date'],

                'total_price'  => $totalPrice,

                'status'       => 'pending',

            ]);



            Payment::create([

                'user_id'        => $userId,

                'booking_id'     => $booking->id,

                'payment_method'=> $data['payment_method'], // wallet | card

                'amount'         => $totalPrice,

                'payment_status' => 'pending',

            ]);



            $this->notificationSender->send(

                $booking->apartment->owner,

                new BookingNotifications($booking, 'pending')

            );



            return $booking;

        });

    }



   public function cancel(int $bookingId, $user): Booking
{
    $booking = Booking::with(['payment', 'apartment.owner'])->findOrFail($bookingId);

    // التحقق من أن المستخدم هو صاحب الحجز
    if ($booking->user_id !== $user->id) throw new \DomainException('UNAUTHORIZED');

    // الحجوزات المكتملة أو الملغاة سابقاً لا يمكن إلغاؤها
    if (in_array($booking->status, ['cancelled', 'completed'])) {
        throw new \DomainException('BOOKING_ALREADY_FINALIZED');
    }

    return DB::transaction(function () use ($booking, $user) {
        $now = now();
        $startDate = Carbon::parse($booking->start_date);
        $hoursUntilBooking = $now->diffInHours($startDate, false);

        $refundAmount = $booking->total_price;

        // منطق الخصم: إذا كان الإلغاء قبل أقل من 24 ساعة من الحجز وهو مؤكد فعلياً
        if ($booking->status === 'confirmed' && $hoursUntilBooking < 24 && $hoursUntilBooking >= 0) {
            $penaltyRate = 0.20; // خصم 20%
            $penaltyAmount = $booking->total_price * $penaltyRate;
            $refundAmount = $booking->total_price - $penaltyAmount;
        }

        // إرجاع المبلغ للمحفظة إذا كان الدفع قد تم
        if ($booking->payment && $booking->payment->payment_status === 'completed') {
            $user->wallet->increment('balance', $refundAmount);
            $booking->payment->update(['payment_status' => 'refunded']);
        }

        $booking->update(['status' => 'cancelled']);

        // إشعار للمالك
        $this->notificationSender->send(
            $booking->apartment->owner,
            new BookingNotifications($booking, 'cancelled')
        );

        return $booking;
    });
}


    public function completePaymentAndConfirm(int $bookingId, array $data, $user): Booking
{
    return DB::transaction(function () use ($bookingId, $data, $user) {
        $booking = Booking::with(['payment', 'user.wallet'])->lockForUpdate()->findOrFail($bookingId);

        // التأكد أن المستخدم هو صاحب الحجز وأن الحالة تسمح بالدفع
        if ($booking->user_id !== $user->id) throw new \DomainException('UNAUTHORIZED');
        if ($booking->status !== 'waiting_payment') throw new \DomainException('NOT_READY_FOR_PAYMENT');

        $payment = $booking->payment;

        // 1. معالجة الدفع حسب الطريقة المختارة
        if ($data['method'] === 'wallet') {
            if ($user->wallet->balance < $payment->amount) {
                throw new \DomainException('INSUFFICIENT_WALLET_BALANCE');
            }
            $user->wallet->decrement('balance', $payment->amount);
        } elseif ($data['method'] === 'credit_card') {
            // هنا تضع كود الربط مع بوابة الدفع (مثل Stripe أو Moyasar)
            // للتجربة سنفترض نجاح العملية دائماً إذا كان الرقم صحيحاً
            if ($data['card_number'] !== '4242424242424242') {
                throw new \DomainException('CARD_PAYMENT_FAILED');
            }
        }

        // 2. تحديث حالة الدفع والحجز
        $payment->update([
            'payment_status' => 'completed',
            'payment_method' => $data['method']
        ]);

        $booking->update(['status' => 'confirmed']);

        // 3. رفض أي حجوزات أخرى متداخلة في نفس الوقت لهذا العقار
        $this->rejectOverlapping($booking);

        return $booking;
    });
}


    private function rejectOverlapping(Booking $booking): void

    {

        Booking::where('apartment_id', $booking->apartment_id)

            ->where('id', '!=', $booking->id)

            ->where('status', 'pending')

            ->where(function ($q) use ($booking) {

                $q->where('start_date', '<=', $booking->end_date)

                  ->where('end_date', '>=', $booking->start_date);

            })

            ->update(['status' => 'rejected']);

    }

     public function showUserBookings($user)
    {
        $bookings=$user->bookings;
        return $bookings;
    }

     public function ownerBookingRequests($user)
    {

       $apartment = Apartment::where('user_id',$user->id)->get();
        $bookings=Booking::whereIn('apartment_id',$apartment->pluck('id'))->where('status','pending')->get();
        return $bookings;
    }

      public function showABook(int $booking_id)
    {
        $booking=Booking::find($booking_id);
        if(!$booking)
        {
            throw new ModelNotFoundException('BOOKING_NOT_FOUND');
        }
        return $booking;
    }

     public function autoCompleteExpiredBookings(): void
    {
        Booking::where('status', 'confirmed')
            ->where('end_date', '<', now()->toDateString())
            ->update([
                'status' =>'completed',
            ]);
    }

}



