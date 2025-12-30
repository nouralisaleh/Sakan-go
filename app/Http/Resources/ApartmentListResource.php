<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                    'id'           => $this->id,
                    'title'        => $this->title,
                    'city'         => $this->city,
                    'governorate'  => $this->governorate,
                    'price'        => $this->price,
                    'images' => $this->whenLoaded('images', function () {
                        return $this->images->map(fn ($img) => asset('storage/' . $img->path));
                    }),                    

                ];   
     }
}
