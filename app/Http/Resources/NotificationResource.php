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
            'id' => $this->id,
            'notification' => [
                'title' => $this->data['title'] ?? '',
                'body' => $this->data['body'] ?? '',
            ],
            'data' => [
                'type' => $this->data['type'] ?? '',
                'action' => $this->data['action'] ?? '',
                'booking_id' => $this->data['booking_id'] ?? null,
                'apartment_id' => $this->data['apartment_id'] ?? null,
                'user_role' => $this->data['user_role'] ?? '',
            ],
            // 'read_at' => $this->read_at,
            // 'created_at' => $this->created_at?->diffForHumans(),
        ];
    }
}
