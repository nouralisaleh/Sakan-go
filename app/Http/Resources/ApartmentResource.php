<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description ?? __(key: 'apartments.description_empty'),
            'city'          => $this->city,
            'governorate'   => $this->governorate,
            'area'          => $this->area,
            'rooms'         => $this->rooms,
            'floor_number'  => $this->floor_number,
            'price'         => $this->price,
            'is_furnished'  => (bool) $this->is_furnished,
            'size'          => $this->size,

            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($img) => asset('storage/' . $img->path));
            }),

            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner->id,
                    'name' => $this->owner->profile ? $this->owner->profile->first_name . ' ' . $this->owner->profile->last_name : null,
                    'phone' => $this->owner->phone_number,
                ];
            }),

        ];
    }

}
