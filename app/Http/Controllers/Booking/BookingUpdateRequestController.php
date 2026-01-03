<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\BookingUpdateRequest;
use App\Service\Booking\BookingUpdateRequestService;
use Illuminate\Http\Request;
use App\Http\Requests\Booking\StoreBookingUpdateRequest;
use App\Http\Resources\BookingUpdateRequestResource;

class BookingUpdateRequestController extends Controller
{
    public function store(
        int $booking,
        StoreBookingUpdateRequest $request,
        BookingUpdateRequestService $service,
    ) {
        $result = $service->store(
            $booking,
            $request->validated(),
            auth('user_api')->user()
        );

        return response()->json([
            'status' => true,
            'data'   => new BookingUpdateRequestResource($result),
            'code'   => 201,
        ], 201);
    }

    public function cancel($id, BookingUpdateRequestService $service)
    {
        $service->cancel($id, auth('user_api')->user());

        return response()->json([
            'status' => true,
            'message'=> __('booking.update_request_cancelled'),
            'code'  => 200,
        ]);
    }

    public function reject($id, BookingUpdateRequestService $service)
    {
        $service->reject($id, auth('user_api')->user());

        return response()->json([
            'status' => true,
            'message'=> __('booking.update_request_rejected'),
            'code'  => 200,
        ]);
    }

    public function approve($id, BookingUpdateRequestService $service)
    {
        $service->confirm($id, auth('user_api')->user());

        return response()->json([
            'status' => true,
            'message'=> __('booking.update_request_approved'),
            'code'  => 200,
        ]);
    }

    public function showOwnerBookingUpdateRequests(BookingUpdateRequestService $service)
    {
        return response()->json([
            'status' => true,
            'data'   => BookingUpdateRequestResource::collection(
                $service->showOwnerBookingUpdateRequests(auth('user_api')->user())
            )   ,
            'code'  => 200,
        ]);
    }

    public function showUserBookingUpdateRequests(BookingUpdateRequestService $service)
    {
        return response()->json([
            'status' => true,
             'data'   => BookingUpdateRequestResource::collection(
                $service->showUserBookingUpdateRequests(auth('user_api')->user())),
             'code'  => 200,

        ]);
    }

    public function show($id, BookingUpdateRequestService $service)
    {
        return response()->json([
            'status' => true,
            'data'   => $service->show($id, auth('user_api')->user()),
            'code'  => 200,
        ]);
    }
}
