<?php 
namespace App\Http\Controllers\Booking;
use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Booking;
use App\Service\Booking\BookingService;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Service\Booking\OwnerConsentService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(int $apartment,StoreBookingRequest $request,BookingService $bookingService) {
        
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
    
            $booking = $bookingService->cancel($bookingId,auth('user_api')->user());

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
        // if($bookings->isEmpty())
        // {
        //     return response()->json([
        //         'status'=> false,
        //         'message'=>__('booking.no_exist_booking'),
        //         'data'=>$boo
        //         'code'=>200,
        //     ]);

        // }
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
            'data'=>new BookingResource($book),
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
public function completeBookingPayment(BookingService $bookingService, Request $request)
{
    $user = auth('user_api')->user();
    $validated = $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'payment_method' => 'required|in:wallet,credit_card',
        'card_number' => 'required_if:payment_method,credit_card|digits:16',
        'cvv' => 'required_if:payment_method,credit_card|digits:3',
        'expiry_date' => 'required_if:payment_method,credit_card|date_format:m/y',
    ]);

    $paymentData = [
        'method' => $request->payment_method,
        'card_number' => $request->card_number ?? null,
    ];

    $booking = $bookingService->completePaymentAndConfirm($request->booking_id, $paymentData, $user);

    return response()->json([
        'status' => true,
        'message' => 'تم الدفع وتأكيد الحجز بنجاح',
        'data' => new BookingResource($booking)
    ]);
}

}


