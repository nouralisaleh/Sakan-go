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
            'phone' =>  $this->phone_number,
            'birth_date' => $this->birth_date ? $this->birth_date->format('Y-m-d') : null,
            'personal_image' => $this->personal_image
                ? url("/api/admin/files/personal")
                : null,

            'id_image' => $this->id_image
                ? url("/api/admin/files/id")
                : null,

        ];
    }
}
