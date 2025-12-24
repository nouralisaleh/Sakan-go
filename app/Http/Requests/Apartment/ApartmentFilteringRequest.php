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
            'city.string'         => __('apartments.validation.city.string'),
            'city.max'            => __('apartments.validation.city.max'),

            'governorate.string'  => __('apartments.validation.governorate.string'),
            'governorate.max'     => __('apartments.validation.governorate.max'),

            'rooms.integer'       => __('apartments.validation.rooms.integer'),
            'rooms.min'           => __('apartments.validation.rooms.min'),

            'area.min'            => __('apartments.validation.area.min'),
            'area.max'      =>__('apartments.validation.area.max'),

            'floor_number.integer'      =>__('apartments.validation.floor_number.integer'),
            'floor_number.min'      =>__('apartments.validation.floor_number.min'),
            
            'is_furnished.boolean'  =>__('apartments.validation.is_furnished.boolean'),

            'price.integer'       => __('apartments.validation.price.integer'),
            'price.min'           => __('apartments.validation.price.min'),  
            
           'size.integer'=> __('apartments.validation.size.integer'),

        ];
    }
}
