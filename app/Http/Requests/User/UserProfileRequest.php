<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            // 'user_id' => 'required|exists:users,id',
            'phone_number' => 'required|numeric|digits_between:5,16',
            'country_code'  => 'required|string|min:2|max:4|starts_with:+',
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'personal_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];
    }
    public function messages()
    {
        return [
            //'user_id.required' => __('validation.required', ['attribute' => 'user id']),
            //'user_id.exists' => __('validation.exists', ['attribute' => 'user id']),
            'first_name.required' => __('validation.required', ['attribute' => 'first name']),
            'first_name.string' => __('validation.string', ['attribute' => 'first name']),
            'first_name.max' => __('validation.max.string', ['attribute' => 'first name', 'max' => 20]),
            'last_name.required' => __('validation.required', ['attribute' => 'last name']),
            'last_name.string' => __('validation.string', ['attribute' => 'last name']),
            'last_name.max' => __('validation.max.string', ['attribute' => 'last name', 'max' => 20]),
            'birth_date.required' => __('validation.required', ['attribute' => 'birth date']),
            'birth_date.date' => __('validation.date', ['attribute' => 'birth date']),
            'personal_image.required' => __('validation.required', ['attribute' => 'personal image']),
            'personal_image.image' => __('validation.image', ['attribute' => 'personal image']),
            'personal_image.mimes' => __('validation.mimes', ['attribute' => 'personal image']),
            'personal_image.max' => __('validation.max.file', ['attribute' => 'personal image', 'max' => 2048]),
            'id_image.required' => __('validation.required', ['attribute' => 'id image']),
            'id_image.image' => __('validation.image', ['attribute' => 'id image']),
            'id_image.mimes' => __('validation.mimes', ['attribute' => 'id image']),
            'id_image.max' => __('validation.max.file', ['attribute' => 'id image', 'max' => 4096]),
        ];
    }
}
