<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth('user_api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stars'=>'required|integer|min:1|max:5',
            'comment'=>'nullable|string|max:1024',
        ];
    }
    public function messages(): array
    {
        return [
            'stars.required'=>__('reviews.validation.rating.required'),
            'stars.integer'=>__('reviews.validation.rating.integer'),
            'stars.min'=>__('reviews.validation.rating.min'),
            'stars.max'=>__('reviews.validation.rating.max'),

            //'comment.required'=>__('reviews.validation.comment.required'),
            'comment.string'=>__('reviews.validation.comment.string'),
            'comment.max'=>__('reviews.validation.comment.max'),
        ];
    }
}
