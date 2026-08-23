<?php

namespace App\Http\Requests\Api\Review;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'score'    => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'content'  => ['required', 'string', 'min:10', 'max:2000'],
            'images'   => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'score'    => 'số sao đánh giá',
            'content'  => 'nội dung đánh giá',
            'images'   => 'danh sách hình ảnh',
            'images.*' => 'hình ảnh',
        ];
    }
}
