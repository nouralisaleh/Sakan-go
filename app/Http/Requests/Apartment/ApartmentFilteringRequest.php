<?php

namespace App\Http\Requests\Apartment;

use Illuminate\Foundation\Http\FormRequest;

class ApartmentFilteringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function authorize(): bool
    {
        return auth('user_api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */


    public function rules(): array
    {
        return [
            'query'        => 'sometimes|string|min:1|max:255',
            'city'         => 'sometimes|string|max:128',
            'governorate'  => 'sometimes|string|max:128',
            'rooms'        => 'sometimes|integer|min:1',
            'area' => 'sometimes|string|min:3|max:128',
            'min_price'        => 'sometimes|numeric|min:0',
            'max_price'        => 'sometimes|numeric|min:0',

            'floor_number'=>  'sometimes|integer|min:0',
            'is_furnished'=>  'nullable|boolean',
            'size'        =>  'sometimes|integer'
        ];
    }
    public function messages()
    {
        return [
            'city.string'         => ('apartments.validation.city.string'),
            'city.max'            => ('apartments.validation.city.max'),

            'governorate.string'  => ('apartments.validation.governorate.string'),
            'governorate.max'     => ('apartments.validation.governorate.max'),

            'rooms.integer'       => ('apartments.validation.rooms.integer'),
            'rooms.min'           => ('apartments.validation.rooms.min'),

            'area.min'            => ('apartments.validation.area.min'),
            'area.max'      =>('apartments.validation.area.max'),

            'floor_number.integer'      =>('apartments.validation.floor_number.integer'),
            'floor_number.min'      =>('apartments.validation.floor_number.min'),

            'is_furnished.boolean'  =>('apartments.validation.is_furnished.boolean'),

            'price.integer'       => ('apartments.validation.price.integer'),
            'price.min'           => ('apartments.validation.price.min'),

           'size.integer'=> ('apartments.validation.size.integer'),

        ];
    }
}
