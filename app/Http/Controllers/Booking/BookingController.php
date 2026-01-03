<?php 
namespace App\Http\Controllers\Booking;
use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Booking;
use App\Service\Booking\BookingService;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Service\Booking\OwnerConsentService;

class BookingController extends Controller
{
    public function store( $apartment,StoreBookingRequest $request,BookingService $bookingService) {
        
            $booking = $bookingService->store(
                auth('user_api')->id(),
                $apartment,
                $request->validated()
            );
    

            return response()->json([
                'status'  => true,
                'message' => __('booking.pending'),
                'data'    => new BookingResource($booking),
                'code'    => 201,
            ], 201);
        }
    public function reject($bookingId, OwnerConsentService $bookingService)
    {
        $user=auth('user_api')->user();
            $booking = $bookingService->reject($bookingId,$user);

            return response()->json([
                'status' => true,
                'message'=> __('booking.rejected'),
                'code'=>200,
            ], 200);


    }
    public function cancel($bookingId, BookingService $bookingService)
    {
    
            $booking = $bookingService->cancel($bookingId);

            return response()->json([
                'status' => true,
                'message'=> __('booking.cancel_ok'),
                'code'=>200,
            ], 200);

    }
    public function showUserBookings(BookingService $bookingService)
    {
        $user=auth('user_api')->user();
        $bookings=$bookingService->showUserBookings($user);
        if($bookings->isEmpty())
        {
            return response()->json([
                'status'=> false,
                'message'=>'No booking ',
                'code'=>200,
            ]);

        }
        return response()->json([
            'status'=> true,
            'data'=>bookingResource::collection($bookings),
            'code'=>200
        ]);

    }
    public function ownerBookingRequests(BookingService $bookingService)
    {
        $user=auth('user_api')->user();
        if($user->role !=='owner')
        {
            return response()->json([
                'status'=> false,
                'message'=>__('auth.only_owner_allowed'),
                'code'=>403,
            ]);
        }
        $bookings=$bookingService->ownerBookingRequests($user);
        return response()->json([
            'status'=> true,
            'data'=>BookingResource::collection($bookings),
            'code'=>200
        ]);

    }
    public function approve(int $booking_id,OwnerConsentService $ownerConsentService)
    {
        $user=auth('user_api')->user();
        $book= $ownerConsentService->approve($booking_id,$user);
        return response()->json([
            'status'=>true,
            'data'=>$book,
            'code'=>200
        ]);
    }
    Public function showABook(int $booking_id,BookingService $bookingService)
    { 
        $booking= $bookingService->showABook($booking_id);
     
        return response()->json([
            'status'=>true,
            'data'=>new BookingResource($booking),
            'code'=>200
        ]);
    }



}


