<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingUpdateRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id'                 => $this->id,
            'booking_id'        => $this->booking_id,
            'status'            => $this->status,
            'update_start_date' => $this->update_start_date,
            'update_end_date'   => $this->update_end_date,

        ];
    }
}
