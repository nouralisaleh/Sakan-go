<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:20',
            'last_name'  => 'sometimes|string|max:20',

            'birth_date' => 'sometimes|date',

            'phone_number' => [
                'sometimes',
                'numeric',
                Rule::unique('users', 'phone_number')->ignore($this->user()->id),
            ],
            'country_code'  => 'sometimes|string|min:2|max:4|starts_with:+',

            'personal_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_image'       => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];
    }
    public function messages()
    {
        return [
            'phone_number.numeric' => __('validation.numeric', ['attribute' => 'phone number']),
            'phone_number.unique' => __('validation.unique', ['attribute' => 'phone number']),
            'first_name.string' => __('validation.string', ['attribute' => 'first name']),
            'first_name.max' => __('validation.max.string', ['attribute' => 'first name', 'max' => 20]),
            'last_name.string' => __('validation.string', ['attribute' => 'last name']),
            'last_name.max' => __('validation.max.string', ['attribute' => 'last name', 'max' => 20]),
            'birth_date.date' => __('validation.date', ['attribute' => 'birth date']),
            'personal_image.image' => __('validation.image', ['attribute' => 'personal image']),
            'personal_image.mimes' => __('validation.mimes', ['attribute' => 'personal image']),
            'personal_image.max' => __('validation.max.file', ['attribute' => 'personal image', 'max' => 2048]),
            'id_image.image' => __('validation.image', ['attribute' => 'id image']),
            'id_image.mimes' => __('validation.mimes', ['attribute' => 'id image']),
            'id_image.max' => __('validation.max.file', ['attribute' => 'id image', 'max' => 4096]),

        ];
    }
}
