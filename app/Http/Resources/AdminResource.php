<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    // Transform the resource into an array.
    
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->country_code . $this->phone_number,
            'birth_date' => $this->birth_date ? $this->birth_date->format('Y-m-d') : null,
            'personal_image' => $this->personal_image
                ? url(path: "https://weepiest-reclinate-cataleya.ngrok-free.dev/api/admin/files/personal/{$this->id}")
                : null,
            'id_image' => $this->id_image
                ? url("https://weepiest-reclinate-cataleya.ngrok-free.dev/api/admin/files/id/{$this->id}")
                : null,
        ];
    }
}
