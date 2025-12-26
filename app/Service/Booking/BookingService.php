<?php
namespace App\Service\Booking;

use App\Models\Apartment;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookingService{

    public function store(int $userId, $apartment_id, array $data): Booking
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

            return Booking::create([
                'user_id'      => $userId,
                'apartment_id' => $apartment->id,
                'start_date'   => $data['start_date'],
                'end_date'     => $data['end_date'],
                'total_price'  =>$totalPrice,
                'status'       => 'pending',
            ]);
        });
    }

    public function cancel($booking_id)
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

        return $booking;
    }
    public function showUserBookings()
    {
        $bookings=auth('user_api')->user()->bookings;
        return $bookings;
    }
    }


 
