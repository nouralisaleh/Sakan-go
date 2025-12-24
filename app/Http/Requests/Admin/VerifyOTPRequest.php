<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

use function Symfony\Component\Translation\t;

class VerifyOTPRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:admins,email',
            'otp' => 'required|numeric|digits:6'
        ];
    }


    public function messages()
    {
        return [
            'email.required' => __('validation.required', ['attribute' => 'email']),
            'email.email' => __('validation.email', ['attribute' => 'email']),
            'email.exists' => __('validation.exists', ['attribute' => 'email']),
            'otp.required' => __('validation.required', ['attribute' => 'OTP']),
            'otp.numeric' => __('validation.numeric', ['attribute' => 'OTP']),
            'otp.digits' => __('validation.digits', ['attribute' => 'OTP', 'digits' => 6]),
        ];
    }

     protected function prepareForValidation()
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
        ]);
    }
}
