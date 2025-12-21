<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:admins,email',
            'new_password' => 'required|min:8|confirmed'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('validation.required', ['attribute' => 'email']),
            'email.email' => __('validation.email', ['attribute' => 'email']),
            'email.exists' => __('validation.exists', ['attribute' => 'email']),
            'new_password.required' => __('validation.required', ['attribute' => 'new password']),
            'new_password.min' => __('validation.min.string', ['attribute' => 'new password', 'min' => 8]),
            'new_password.confirmed' => __('validation.confirmed', ['attribute' => 'new password']),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
        ]);
    }
}
