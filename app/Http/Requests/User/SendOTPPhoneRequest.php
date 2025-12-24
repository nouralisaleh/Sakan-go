<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SendOTPPhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'phone_number' => 'required|digits_between:6,15|numeric',
            'country_code' => 'required|string|min:2|max:4|starts_with:+'
        ];
    }
    public  function messages()
    {
        return [
            'phone_number.numeric' => __('validation.numeric', ['attribute' => 'phone number']),
            'phone_number.required' => __('validation.required', ['attribute' => 'phone_number']),
            'phone_number.digits_between' => __('validation.digits_between', ['attribute' => 'phone_number', 'min' => 6, 'max' => 15]),
            'country_code.string' => __('validation.string', ['attribute' => 'country code']),
            'country_code.starts_with' => __('validation.starts_with', ['attribute' => 'country code']),
            'country_code.min' => __('validation.min.string', ['attribute' => 'country code', 'min' => 2]),
            'country_code.max' => __('validation.max.string', ['attribute' => 'country code', 'max' => 4]),
        ];
    }
}
