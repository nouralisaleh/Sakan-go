<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOTPPhoneRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'phone_number' => 'required|regex:/^09[3-9]\d{7}$/|numeric',
            'country_code'  => 'required|numeric|regex:/^\+963$/',
            'otp' => 'required|numeric|digits:6'
        ];
    }
    public function messages()
    {
        return [

            'phone_number.required' => __('validation.required', ['attribute' => 'phone_number']),
            'phone_number.numeric' => __('validation.numeric', ['attribute' => 'phone number']),
            'phone_number.regex' => __('validation.regex', ['attribute' => 'phone number']),
            'country_code.numeric' => __('validation.numeric', ['attribute' => 'country code']),
            'country_code.regex' => __('validation.regex', ['attribute' => 'country code']),
            'country_code.required' => __('validation.required', ['attribute' => 'country code']),
            'otp.required' => __('validation.required', ['attribute' => 'OTP']),
            'otp.numeric' => __('validation.numeric', ['attribute' => 'OTP']),
            'otp.digits' => __('validation.digits', ['attribute' => 'OTP', 'digits' => 6]),
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'phone_number' => trim($this->phone_number),
            'country_code' => trim($this->country_code),
        ]);
    }
}
