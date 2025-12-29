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

            'name' => 'sometimes|string|max:100',


            'birth_date' => 'sometimes|date',

            'phone_number'  => 'sometimes|numeric|regex:/^09[3-9]\d{7}$',
            'country_code'  => 'sometimes|numeric|regex:/^\+963$/',

            'personal_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_image'       => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];
    }
    public function messages()
    {
        return [
            'email.email'       => __('validation.email', ['attribute' => 'email']),
            'email.unique'      => __('validation.unique', ['attribute' => 'email']),

            'name.string' => __('validation.string', ['attribute' => 'name']),
            'name.max'    => __('validation.max.string', ['attribute' => 'name', 'max' => 100]),

            'birth_date.date'   => __('validation.date', ['attribute' => 'birth date']),

            'phone_number.numeric' => __('validation.numeric', ['attribute' => 'phone number']),
            'phone_number.regex' => __('validation.regex', ['attribute' => 'phone number']),

            'country_code.numeric' => __('validation.numeric', ['attribute' => 'country code']),
            'country_code.regex' => __('validation.regex', ['attribute' => 'country code']),

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
     protected function prepareForValidationPhone()
    {
        $this->merge([
            'phone_number' => trim($this->phone_number),
            'country_code' => trim($this->country_code),
        ]);
    }
}
