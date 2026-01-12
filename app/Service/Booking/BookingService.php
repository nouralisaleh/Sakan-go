<?php
namespace App\Service\Booking;

use App\Models\Apartment;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Notifications\BookingNotifications;
use App\Service\Notifications\NotificationSender;
use App\Models\Payment;



class BookingService{
    protected  $notificationSender;
    public function __construct(NotificationSender $notificationSender)
    {
        $this->notificationSender = $notificationSender;
    }

    public function store(int $userId, int $apartment_id, array $data): Booking
    {
        $apartment=Apartment::find($apartment_id);
        if(!$apartment){
            throw new ModelNotFoundException('APARTMENT_NOT_FOUND');
        }

        return DB::transaction(function () use ($userId, $apartment, $data) {

            $overlapping = Booking::where('apartment_id', $apartment->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($q) use ($data) {
                    $q->where('start_date', '<=', $data['end_date'])
                      ->where('end_date', '>=', $data['start_date']);
                })
                ->lockForUpdate()
                ->exists();

            if ($overlapping) {
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
                'total_price'  =>$totalPrice,
                'status'       => 'pending',
            ]);
            Payment::create([
                'user_id' => $userId,
                'booking_id' => $booking->id,
                'payment_method'     => $data['payment_method'], // wallet | card
                'amount'     => $totalPrice,
                'payment_status'     => 'pending',
            ]);
            $notification = new BookingNotifications($booking, 'pending');
            // $booking->apartment->owner->notify($notification);
            // $notification->sendFcm($booking->apartment->owner);

           $this->notificationSender->send($booking->apartment->owner,$notification);



            return $booking;
        });
    }


    public function cancel(int $booking_id)
    {
        $booking = Booking::find($booking_id);

        if (!$booking) {
            throw new ModelNotFoundException('BOOKING_NOT_FOUND');
        }

        if (in_array($booking->status, ['rejected', 'cancelled'])) {
            throw new \DomainException('BOOKING_ALREADY_FINALIZED');
        }

        $booking->update([
            'status' => 'cancelled',
        ]);
       if ($booking->payment && $booking->payment->payment_status === 'pending') {
            $booking->payment->update([
                'payment_status' => 'failed',
            ]);
        }
        $notification = new BookingNotifications($booking, 'cancelled');

        $this->notificationSender->send($booking->apartment->owner,$notification);

        return $booking;
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


 
