<?php

namespace App\Service\Apartment;

use App\Models\Apartment;
use Illuminate\Support\Facades\Storage;

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
}
