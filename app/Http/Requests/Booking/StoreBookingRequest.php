<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date'=>'required|date',
            'end_date'=>'required|date|after:start_date'
        ];
    }
    public function messages(): array
    {
        return [
            'start_date.required'     => __('booking.start_date.required'),
            'start_date.date'         => __('booking.date'),

            'end_date.required'       => __('booking.end_date.required'),
            'end_date.date'           =>  __('booking.date'),
            'end_date.after_or_equal' => __('booking.end_date.after_or_equal'),
        ];
    }
}
