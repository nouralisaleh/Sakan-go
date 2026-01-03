<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       return  auth('user_api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'update_start_date'=>'required|date',
        'update_end_date'=>'required|date|after_or_equal:update_start_date',
        ];
    }
    public function messages()
    {
        return [
  
        'update_start_date.required'=>__('booking.update_start_date.required'),
        'update_start_date.date'=>__('booking.update_start_date.date'),
        'update_start_date.after_or_equal'=>__('booking.update_start_date.after_or_equal'),

        'update_end_date.required'=>__('booking.update_end_date.required'),
        'update_end_date.date'=>__('booking.update_end_date.date'),
        'update_end_date.after_or_equal'=>__('booking.update_end_date.after_or_equal'),
        ];
    }
}
