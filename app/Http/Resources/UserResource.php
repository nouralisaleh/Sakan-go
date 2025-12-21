<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
         return [
        'id' => $this->id,
        'status' => $this->status,
        'role'=>$this->role,

        'phone' => $this->country_code . $this->phone_number,

        'profile' => [
            'first_name' => $this->profile?->first_name,
            'last_name'  => $this->profile?->last_name,
            'birth_date' => $this->profile?->birth_date,

            'personal_image' => $this->profile->personal_image
                ? url("/api/files/personal/{$this->id}")
                : null,

            'id_image' => $this->profile->id_image
                ? url("/api/files/id/{$this->id}")
                : null,
        ],

        'rejected_reason' => $this->rejected_reason,
        'created_at' => $this->created_at,
    ];

    }
}
