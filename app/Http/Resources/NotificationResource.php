<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
           // 'type'       => class_basename($this->type),
            'read_at'    => $this->read_at,
            'created_at'=> $this->created_at?->diffForHumans(),

            'data' => [
                'booking_id'   => $this->data['booking_id'] ?? null,
                'apartment_id' => $this->data['apartment_id'] ?? null,
                //'status'       => $this->data['status'] ?? null,
                'message'      => $this->data['message'] ?? null,
            ],
        ];
    }
}
