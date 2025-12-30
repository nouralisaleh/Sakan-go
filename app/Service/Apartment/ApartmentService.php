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

  public function update( Apartment $apartment, array $data, array $images = []): Apartment
    { 
        //if($apartment->bookings()){

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
    // public function ownerApartments()
    // {

    // }

  
    

}