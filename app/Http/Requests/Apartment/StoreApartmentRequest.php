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

     return auth('user_api')->check();
    }




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
            'images.*'  => 'required|image|mimes:jpg,jpeg,png',

        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => ('apartments.validation.title.required'),
            'title.string'         => ('apartments.validation.title.string'),
            'title.min'            => ('apartments.validation.title.min'),
            'title.max'            => ('apartments.validation.title.max'),

            //'description.required' => ('apartments.validation.description.required'),
            'description.string'   => ('apartments.validation.description.string'),
            'description.max'      => ('apartments.validation.description.max'),

            'city.required'        => ('apartments.validation.city.required'),
            'city.string'          => ('apartments.validation.city.string'),
            'city.max'             => ('apartments.validation.city.max'),

            'governorate.required' => ('apartments.validation.governorate.required'),
            'governorate.string'   => ('apartments.validation.governorate.string'),
            'governorate.max'      => ('apartments.validation.governorate.max'),

            'rooms.required'       => ('apartments.validation.rooms.required'),
            'rooms.min'            => ('apartments.validation.rooms.min'),

            'area.required'        => ('apartments.validation.area.required'),
            'area.min'      =>('apartments.validation.area.min'),
            'area.max'      =>('apartments.validation.area.max'),


            'floor_number.required'      =>('apartments.validation.floor_number.required'),
            'floor_number.integer'      =>('apartments.validation.floor_number.integer'),
            'floor_number.min'      =>('apartments.validation.floor_number.min'),

            'is_furnished.boolean'  =>('apartments.validation.is_furnished.boolean'),

            'price.required'       => ('apartments.validation.price.required'),
            'price.integer'        => ('apartments.validation.price.integer'),
            'price.min'            => ('apartments.validation.price.min'),
           // 'price.max'        =>('apartment.validation.price.max'),
           'size.required' => ('apartments.validation.size.required'),
           'size.integer'=> ('apartments.validation.size.integer'),

            'images.array'         => ('apartments.validation.images.array'),
            'images.required'         => ('apartments.validation.images.required'),
            'images.*.image'       => ('apartments.validation.images.image'),
            'images.*.max'         => ('apartments.validation.images.max'),
        ];
    }
}
