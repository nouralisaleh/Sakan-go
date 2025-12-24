<?php

namespace App\Service\Apartment;

use App\Http\Requests\UpdateApartmentRequest;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class ApartmentService
{
    public function __construct(
            public  ApartmentImagesService $imageService
        ) {}

    public function show()
    {
        return Apartment::latest()->get();
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

  public function update(Apartment $apartment, array $data, array $images = []): Apartment
    {
        return DB::transaction(function () use ($apartment, $data, $images) {
            $apartment->update($data);

            if (!empty($images)) {
                $this->imageService->deleteApartmentImages($apartment);
                $this->imageService->storeApartmentImages($apartment, $images);
            }

            return $apartment->fresh();
        });
    }

   public function delete(Apartment $apartment): bool
    {
        return DB::transaction(function () use ($apartment) {
            $this->imageService->deleteApartmentImages($apartment);
            return $apartment->delete();
        });
    }

    public function apartmentOwner(Apartment $apartment)
    {
        return $apartment->owner;
    }
    

}