<?php

namespace App\Service\Apartment;

use App\Http\Requests\UpdateApartmentRequest;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;




class ApartmentService
{
    public function __construct(
            public  ApartmentImagesService $imageService
        ) {}

    public function show()
    {
        return Apartment::with('images')->latest()->get();
    }

  public function store(array $data, array $images=[], int $userId): Apartment
{
    return DB::transaction(function () use ($data, $images, $userId) {

        $apartment = Apartment::create(
            $data + ['user_id' => $userId]
        );

        if (!empty($images)) {
            $this->imageService->storeApartmentImages($apartment, $images);
        }

        return $apartment->fresh();
    });
}

  public function update(int  $apartment_id, array $data, array $images = [],$user)
    { 

         $apartment=Apartment::where('id',$apartment_id)->first();
        if(!$apartment)
        {
           throw new ModelNotFoundException('APARTMENT_NOT_FOUND');
        } 

        if ($apartment->user_id !== $user->id)
        {
           throw new \DomainException('NOT_APARTMENT_OWNER');
        }
        $this->ensureNotBooked($apartment);

        return DB::transaction(function () use ($apartment, $data, $images) {
            $apartment->update($data);

            if (!empty($images)) {
                $this->imageService->deleteApartmentImages($apartment);
                $this->imageService->storeApartmentImages($apartment, $images);
            }

            return $apartment->fresh();
        });
    }

   public function delete( $apartment_id): bool
    {
       $apartment=Apartment::find($apartment_id);
       if( !$apartment )
        {
           throw new ModelNotFoundException('APARTMENT_NOT_FOUND');
        } 
        $this->ensureNotBooked($apartment);
 
        return DB::transaction(function () use ($apartment) {
            $this->imageService->deleteApartmentImages($apartment);
            return $apartment->delete();
        });
    }

    public function apartmentOwner( $apartment_id)
    {
       $apartment=Apartment::find($apartment_id);
       if( !$apartment )
        {
           throw new ModelNotFoundException('APARTMENT_NOT_FOUND');
        }
        return $apartment->owner->profile();
    }

    private function ensureNotBooked(Apartment $apartment): void
{
    $hasActiveBookings = $apartment->bookings()
        ->whereIn('status', ['pending', 'confirmed'])
        ->exists();

    if ($hasActiveBookings) {
        throw new \DomainException('APARTMENT_HAS_ACTIVE_BOOKINGS');
    }
}
   public function showAnApartment($apartmentId)
   {
       $apartment=Apartment::with('images')->find($apartmentId);
       if(!$apartment)
       {
          throw new ModelNotFoundException('APARTMENT_NOT_FOUND');
       }
       return $apartment;
   }

  
    

}