<?php

namespace App\Service\Apartment;

use App\Models\Apartment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApartmentImagesService
{
    public function storeApartmentImages(Apartment $apartment, array $images): void
    {
        foreach ($images as $image) {
            $path = $image->store("apartments/{$apartment->id}", 'public');

            $apartment->images()->create([
                'path' => $path
            ]);
        }
    }

    public function deleteApartmentImages(Apartment $apartment): void
    {
        foreach ($apartment->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }
    public function showApartmentImages( $apartment_id)
    {
       $apartment=Apartment::with('images')->find($apartment_id);
       if(!$apartment)
       {
          throw new ModelNotFoundException('APARTMENT_NOT_FOUND');
       }
        return $apartment->images;
    }
}
