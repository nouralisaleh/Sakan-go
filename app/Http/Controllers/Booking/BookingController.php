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
            ], 201);
        }
    public function reject($bookingId, OwnerConsentService $bookingService)
    {
            $booking = $bookingService->reject($bookingId);

            return response()->json([
                'status' => true,
                'message'=> __('booking.rejected'),
                //'data' =>new BookingResource($booking),
            ], 200);


    }
    public function cancel($bookingId, BookingService $bookingService)
    {
    
            $booking = $bookingService->cancel($bookingId);

            return response()->json([
                'status' => true,
                'message'=> __('booking.cancel_ok'),
                //'data' =>new BookingResource($booking),
            ], 200);

    }public function showUserBookings(BookingService $bookingService)
    {
    $bookings=$bookingService->showUserBookings();
    if(!$bookings)
    {
        return response()->json([
            'status'=> false,
            'message'=>'No booking '
        ]);

    }
    return response()->json([
        'status'=> true,
        'data'=>bookingResource::collection($bookings),
    ]);

    }
    public function ownerRequests()
     {  
    //     $user = auth('user_api')->user();
    //     if($user->role !== 'owner')
    //     {
    //       return response()->json([
    //         'status'=> false,
    //         'message'=>'لست مخول للقيام باي شي يخص المالك',
    //       ]);

    //     }
    //     $apartments=Apartment::where('user_id', $user->id)->get();
    //     return response()->json([
    //         'status'=> true,
    //         'data'=> $apartments,
    //     ]);

    }
    public function approve($booking_id,OwnerConsentService $ownerConsentService)
    {
        $book= $ownerConsentService->approve($booking_id);
        return response()->json([
            'data'=>$book
        ]);
    }



}


