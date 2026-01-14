<?php
namespace App\Service\Review;
use App\Models\Apartment;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReviewService{

    public function store(int $bookingId, array $data, $user): Review
    {
        $booking = Booking::with('review')->find($bookingId);

        if (!$booking) {
            throw new ModelNotFoundException('BOOKING_NOT_FOUND');
        }

        if ($booking->user_id !== $user->id) {
            throw new \DomainException('NOT_BOOKING_OWNER');
        }

        if ($booking->status !== 'completed') {
            throw new \DomainException('BOOKING_NOT_COMPLETED');
        }

        if ($booking->review) {
            throw new \DomainException('REVIEW_ALREADY_EXISTS');
        }

        return Review::create([
            'user_id'      => $user->id,
            'booking_id'   => $booking->id,
            'apartment_id' => $booking->apartment_id,
            'stars'       => $data['stars'],
            'comment'      => $data['comment'] ?? null,
        ]);
    }
    public function getApartmentReview($apartment_id)
    {
        $apartment = Apartment::find($apartment_id);

        if (!$apartment) {
            throw new ModelNotFoundException('APARTMENT_NOT_FOUND');
        }
        return $apartment->getAverageRatingAttribute();


    }
}


