<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Service\Review\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request,ReviewService $reviewService,$bookingId)
    {
        $user=auth('user_api')->user();
        $review=$reviewService->store($bookingId,$request->validated(),$user);
        return response()->json([
            'status'=>true,
            'message'=>__('reviews.created'),
            'data'=>new ReviewResource($review),
            'code'=>201,
        ],201);
    }
    public function getApartmentReview(ReviewService $reviewService,$apartment_id)
    {
        $averageRating=$reviewService->getApartmentReview($apartment_id);
        return response()->json([
            'status'=>true,
            'message'=>__('reviews.fetched'),
            'data'=>['average_rating'=>$averageRating],
            'code'=>200,
        ],200);
    }
}
