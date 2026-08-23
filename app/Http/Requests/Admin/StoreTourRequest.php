<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,category_id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'highlights' => ['nullable', 'string'],
            'departure_location' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'included_services' => ['nullable', 'string'],
            'excluded_services' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Vui lòng chọn danh mục cho Tour.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            'title.required' => 'Tên Tour không được để trống.',
            'title.max' => 'Tên Tour không vượt quá 255 ký tự.',
            'price.required' => 'Giá Tour không được để trống.',
            'price.numeric' => 'Giá Tour phải là số.',
            'price.min' => 'Giá Tour không được nhỏ hơn 0.',
            'duration_days.required' => 'Số ngày Tour không được để trống.',
            'duration_days.integer' => 'Số ngày Tour phải là số nguyên.',
            'duration_days.min' => 'Số ngày Tour tối thiểu là 1.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
