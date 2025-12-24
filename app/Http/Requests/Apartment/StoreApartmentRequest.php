<?php

namespace App\Http\Requests\Apartment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;



class StoreApartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize(): bool
    {

     return true;
    }

// protected function failedAuthorization()
// {
//     throw new AuthorizationException(
//         __('apartment.only_owner_allowed')
//     );
// }


    public function rules(): array
    {
        return [
            'title' => 'required|string|min:10|max:512',
            'description'  => 'nullable|string|max:512',
            'city'         => 'required|string|max:128',
            'governorate'  => 'required|string|max:128',
            'rooms'        => 'required|integer|min:1',
            'area' => 'required|string|min:3|max:128',
            'price'        => 'required|integer|min:0',
            'floor_number'=>  'required|integer|min:0',
            'is_furnished'=>  'nullable|boolean',
            'size'        =>  'required|integer|min:30',
            'images' => 'required|array|min:1',
            'images.*'  => 'required|image|mimes:jpg,jpeg,png|max:2048',

        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => __('apartments.validation.title.required'),
            'title.string'         => __('apartments.validation.title.string'),
            'title.min'            => __('apartments.validation.title.min'),
            'title.max'            => __('apartments.validation.title.max'),

            //'description.required' => __('apartments.validation.description.required'),
            'description.string'   => __('apartments.validation.description.string'),
            'description.max'      => __('apartments.validation.description.max'),

            'city.required'        => __('apartments.validation.city.required'),
            'city.string'          => __('apartments.validation.city.string'),
            'city.max'             => __('apartments.validation.city.max'),

            'governorate.required' => __('apartments.validation.governorate.required'),
            'governorate.string'   => __('apartments.validation.governorate.string'),
            'governorate.max'      => __('apartments.validation.governorate.max'),

            'rooms.required'       => __('apartments.validation.rooms.required'),
            'rooms.min'            => __('apartments.validation.rooms.min'),

            'area.required'        => __('apartments.validation.area.required'),
            'area.min'      =>__('apartments.validation.area.min'),
            'area.max'      =>__('apartments.validation.area.max'),


            'floor_number.required'      =>__('apartments.validation.floor_number.required'),
            'floor_number.integer'      =>__('apartments.validation.floor_number.integer'),
            'floor_number.min'      =>__('apartments.validation.floor_number.min'),
            
            'is_furnished.boolean'  =>__('apartments.validation.is_furnished.boolean'),

            'price.required'       => __('apartments.validation.price.required'),
            'price.integer'        => __('apartments.validation.price.integer'),
            'price.min'            => __('apartments.validation.price.min'),
           // 'price.max'        =>__('apartment.validation.price.max'),
           'size.required' => __('apartments.validation.size.required'),
           'size.integer'=> __('apartments.validation.size.integer'),

            'images.array'         => __('apartments.validation.images.array'),
            'images.required'         => __('apartments.validation.images.required'),
            'images.*.image'       => __('apartments.validation.images.image'),
            'images.*.max'         => __('apartments.validation.images.max'),
        ];
    }
}
