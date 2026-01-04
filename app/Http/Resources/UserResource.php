<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    // Transform the resource into an array.
    public function toArray(Request $request): array
    {
         return [
        'id' => $this->id,
        'status' => $this->status,
        'role'=>$this->role,

        'phone' => $this->phone_number,

        'profile' => [
            'first_name' => $this->profile?->first_name,
            'last_name'  => $this->profile?->last_name,
            'birth_date' => $this->birth_date ? $this->birth_date->format('Y-m-d') : null,
            'personal_image' => $this->profile->personal_image
                ? url(path: "https://weepiest-reclinate-cataleya.ngrok-free.dev/api/user/files/personal/{$this->id}")
                : null,

            'id_image' => $this->profile->id_image
                ? url("https://weepiest-reclinate-cataleya.ngrok-free.dev/api/user/files/id/{$this->id}")
                : null,
        ],
        'rejected_reason' => $this->rejected_reason,
    ];

    }
}
