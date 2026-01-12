<?php
namespace App\Service\Booking;

use App\Models\Booking;
use App\Models\BookingUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Notifications\BookingNotifications;
use App\Service\Notifications\NotificationSender;

use DomainException;

class BookingUpdateRequestService
{
    protected  $notificationSender;
    public function __construct(NotificationSender $notificationSender)
    {
        $this->notificationSender = $notificationSender;
    }
    public function store(int $bookingId, array $data, $user): BookingUpdateRequest
    {
        $booking = Booking::find($bookingId);

        if (!$booking) {
            throw new ModelNotFoundException('BOOKING_NOT_FOUND');
        }

        if ($booking->user_id !== $user->id) {
            throw new DomainException('FORBIDDEN_ACTION');
        }

        if (in_array($booking->status, ['cancelled', 'rejected', 'completed'])) {
            throw new DomainException('BOOKING_ALREADY_FINALIZED');
        }

        return DB::transaction(function () use ($booking, $data,$user) {

            $overlapping = Booking::where('apartment_id', $booking->apartment_id)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($q) use ($data) {
                    $q->where('start_date', '<=', $data['update_end_date'])
                      ->where('end_date', '>=', $data['update_start_date']);
                })
                ->lockForUpdate()
                ->exists();

            if ($overlapping) {
                throw new DomainException('BOOKING_CONFLICT');
            }

            if ($booking->status === 'pending') {
                $booking->update([
                    'start_date' => $data['update_start_date'],
                    'end_date'   => $data['update_end_date'],
                ]);


                return BookingUpdateRequest::create([
                    'booking_id'        => $booking->id,
                    'update_start_date' => $data['update_start_date'],
                    'update_end_date'   => $data['update_end_date'],
                    'status'            => 'confirmed',
                ]);
            }
            if (!empty($data['payment_method'])) {
                    $booking->payment?->update([
                        'payment_method' => $data['payment_method'],
                    ]);
                }

        $updateRequest = BookingUpdateRequest::create([
                        'booking_id'        => $booking->id,
                        'update_start_date' => $data['update_start_date'],
                        'update_end_date'   => $data['update_end_date'],
                        'status'            => 'pending',
                    ]);
          
            $notification = new BookingNotifications($booking, 'update_request');
            $this->notificationSender->send($booking->apartment->owner,$notification);


            return $updateRequest;
        });
    }

    public function cancel(int $UpdateRequestId, $user): BookingUpdateRequest
    {
        $request = BookingUpdateRequest::find($UpdateRequestId);

        if (!$request) {
            throw new ModelNotFoundException('BOOKING_UPDATE_REQUEST_NOT_FOUND');
        }
        if($request->booking->user_id !== $user->id)
        {
            throw new DomainException('FORBIDDEN_ACTION');
        }

        if ($request->booking->user_id !== $user->id) {
            throw new DomainException('FORBIDDEN_ACTION');
        }

        if ($request->status !== 'pending') {
            throw new DomainException('BOOKING_ALREADY_FINALIZED');
        }

        $request->update(['status' => 'cancelled']);

        $notification = new BookingNotifications($request->booking, 'cancelled');

        $this->notificationSender->send($request->booking->apartment->owner,$notification);


        return $request;
    }

    public function reject(int $UpdateRequestId, $user): BookingUpdateRequest
    {
        $request = BookingUpdateRequest::find($UpdateRequestId);

        if (!$request) {
            throw new ModelNotFoundException('BOOKING_UPDATE_REQUEST_NOT_FOUND');
        }

        if ($request->booking->apartment->user_id !== $user->id) {
            throw new DomainException('NOT_APARTMENT_OWNER');
        }


        if ($request->status !== 'pending') {
            throw new DomainException('BOOKING_ALREADY_FINALIZED');
        }

        $request->update(['status' => 'rejected']);

        $notification = new BookingNotifications($request->booking, 'rejected');
        $this->notificationSender->send($request->booking->user,$notification);

        return $request;
    }

    public function confirm(int $id, $user): BookingUpdateRequest
    {
        return DB::transaction(function () use ($id, $user) {

            $request = BookingUpdateRequest::find($id);

            if (!$request) {
                throw new ModelNotFoundException('BOOKING_UPDATE_REQUEST_NOT_FOUND');
            }

            if ($request->booking->apartment->user_id !== $user->id) {
                throw new DomainException('NOT_APARTMENT_OWNER');
            }

            if ($request->status !== 'pending') {
                throw new DomainException('BOOKING_ALREADY_FINALIZED');
            }

            BookingUpdateRequest::where('id', '!=', $request->id)
                ->where('status', 'pending')
                ->whereHas('booking', function ($q) use ($request) {
                    $q->where('apartment_id', $request->booking->apartment_id)
                      ->where(function ($qq) use ($request) {
                          $qq->where('start_date', '<=', $request->update_end_date)
                             ->where('end_date', '>=', $request->update_start_date);
                      });
                })
                ->update(['status' => 'rejected']);

            $request->update(['status' => 'confirmed']);

            $request->booking->update([
                'start_date' => $request->update_start_date,
                'end_date'   => $request->update_end_date,
            ]);

            $notification = new BookingNotifications($request->booking, 'approved');
            $this->notificationSender->send($request->booking->user,$notification);


            return $request;
        });
    }

    public function showOwnerBookingUpdateRequests($user)
    {
        return BookingUpdateRequest::whereHas('booking.apartment', fn ($q) =>
            $q->where('user_id', $user->id)
        )->get();
    }

    public function showUserBookingUpdateRequests($user)
    {
        return BookingUpdateRequest::whereHas('booking', fn ($q) =>
            $q->where('user_id', $user->id)
        )->get();
    }

    public function show(int $id, $user): BookingUpdateRequest
    {
        $request = BookingUpdateRequest::find($id);

        if (!$request) {
            throw new ModelNotFoundException('BOOKING_UPDATE_REQUEST_NOT_FOUND');
        }

        if ($request->booking->user_id !== $user->id) {
            throw new DomainException('FORBIDDEN_ACTION');
        }

        return $request;
    }
}
