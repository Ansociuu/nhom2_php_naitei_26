<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourImageRequest extends FormRequest
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
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'images.required' => 'Vui lòng chọn ít nhất 1 hình ảnh.',
            'images.array' => 'Dữ liệu hình ảnh tải lên không hợp lệ.',
            'images.min' => 'Vui lòng chọn ít nhất 1 hình ảnh.',
            'images.*.required' => 'File ảnh không được để trống.',
            'images.*.file' => 'Dữ liệu phải là tệp tin.',
            'images.*.image' => 'Tệp tin phải là định dạng hình ảnh.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, webp.',
            'images.*.max' => 'Dung lượng mỗi ảnh không vượt quá 5MB.',
        ];
    }
}
