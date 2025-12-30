<?php

namespace App\Http\Requests\Apartment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;




class UpdateApartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
     public function authorize(): bool
    {
    $apartment = $this->route('apartment');
    
    return $apartment->user_id === auth('user_api')->id();
    }

    public function rules(): array
    {
        return [
            
            'title' => 'sometimes|string|min:10|max:512',
            'description'  => 'sometimes|string|max:512',
            'city'         => 'sometimes|string|max:128',
            'governorate'  => 'sometimes|string|max:128',
            'rooms'        => 'sometimes|integer|min:1',
            'area' => 'sometimes|string|min:3|max:128',
            'price'        => 'sometimes|numeric|min:0',
            'floor_number'=>  'sometimes|integer|min:0',
            'is_furnished'=>  'nullable|boolean',
            'images' =>       'sometimes|array|min:1',
            'images.*'  =>    'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'size'        =>  'sometimes|integer|min:30'


        ];
    }

    public function messages(): array
    {
        return [
            'title.string'        => __('apartments.validation.title.string'),
            'title.min'           => __('apartments.validation.title.min'),
            'title.max'           => __('apartments.validation.title.max'),

            'description.string'  => __('apartments.validation.description.string'),
            'description.max'     => __('apartments.validation.description.max'),

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
           // 'price.numeric'        =>__('apartment.validation.price.numeric'),
           'size.integer'=> __('apartments.validation.size.integer'),

            'images.array'         => __('apartments.validation.images.array'),
            'images.*.image'       => __('apartments.validation.images.image'),
            'images.*.max'         => __('apartments.validation.images.max'),

        ];
    }
}
