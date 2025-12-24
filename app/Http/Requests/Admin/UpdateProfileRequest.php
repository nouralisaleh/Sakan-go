<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => [
                'sometimes',
                'email',
                Rule::unique('admins', 'email')->ignore($this->user()->id),
            ],

            'first_name' => 'sometimes|string|max:20',
            'last_name'  => 'sometimes|string|max:20',

            'birth_date' => 'sometimes|date',

            'phone_number'  => 'sometimes|string|digits_between:6,15',
            'country_code'  => 'sometimes|string|min:2|max:4|starts_with:+',

            'personal_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_image'       => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];
    }

    public function messages()
    {
        return [
           'email.email'       => __('validation.email', ['attribute' => 'email']),
           'email.unique'      => __('validation.unique', ['attribute' => 'email']),

           'first_name.string' => __('validation.string', ['attribute' => 'first name']),
           'first_name.max'    => __('validation.max.string', ['attribute' => 'first name', 'max' => 20]),

           'last_name.string'  => __('validation.string', ['attribute' => 'last name']),
           'last_name.max'     => __('validation.max.string', ['attribute' => 'last name', 'max' => 20]),

           'birth_date.date'   => __('validation.date', ['attribute' => 'birth date']),

           'phone_number.string' => __('validation.string', ['attribute' => 'phone number']),
           'phone_number.digits_between' => __('validation.digits_between', ['attribute' => 'phone number', 'min' => 6, 'max' => 15]),

           'country_code.string' => __('validation.string', ['attribute' => 'country code']),
           'country_code.starts_with' => __('validation.starts_with', ['attribute' => 'country code']),
           'country_code.min'=>__('validation.min.string', ['attribute' => 'country code', 'min' => 2]),


           'country_code.max'    =>__('validation.max.string', ['attribute' => 'country code', 'max' => 4]),



           'personal_image.image' => __('validation.image', ['attribute' => 'personal image']),
           'personal_image.mimes' => __('validation.mimes', ['attribute' => 'personal image', 'values' => 'jpeg, png, jpg, gif']),
           'personal_image.max'   => __('validation.max.file', ['attribute' => 'personal image', 'max' => 2048]),

           'id_image.image' => __('validation.image', ['attribute' => 'ID image']),
           'id_image.mimes' => __('validation.mimes', ['attribute' => 'ID image', 'values' => 'jpeg, png, jpg, gif']),
           'id_image.max'   => __('validation.max.file', ['attribute' => 'ID image', 'max' => 4096]),


        ];
    }

    protected function prepareForValidation()
{
    if ($this->has('email')) {
        $this->merge([
            'email' => strtolower(trim($this->email)),
        ]);
    }
}
}
