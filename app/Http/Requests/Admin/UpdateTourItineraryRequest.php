<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTourItineraryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'day_number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'day_number.required' => 'Vui lòng nhập thứ tự ngày.',
            'day_number.integer' => 'Thứ tự ngày phải là số nguyên.',
            'day_number.min' => 'Thứ tự ngày phải lớn hơn hoặc bằng 1.',
            'title.required' => 'Vui lòng nhập tiêu đề ngày lịch trình.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
        ];
    }
}
