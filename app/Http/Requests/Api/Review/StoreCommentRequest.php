<?php

namespace App\Http\Requests\Api\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'content'           => ['required', 'string', 'min:2', 'max:1000'],
            'parent_comment_id' => ['nullable', 'integer', 'exists:comments,comment_id'],
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
            'content'           => 'nội dung bình luận',
            'parent_comment_id' => 'bình luận cha',
        ];
    }
}
