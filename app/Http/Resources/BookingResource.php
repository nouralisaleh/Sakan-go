<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'user_id'=> $this->user_id,
            'apartment_id'=>$this->apartment_id,
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'total_price'=>$this->total_price,
            'status'=> $this->status,
         
            'payment' => $this->when($this->payment, [
                'method' => $this->payment?->payment_method,
                'status' => $this->payment?->payment_status,
                'amount' => $this->payment?->amount,
            ]),
        ];
    }
}
