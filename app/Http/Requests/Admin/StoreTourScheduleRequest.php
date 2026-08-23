<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTourScheduleRequest extends FormRequest
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
        $tour = $this->route('tour');
        $tourId = $tour ? $tour->tour_id : null;

        return [
            'departure_date' => [
                'required',
                'date',
                Rule::unique('tour_schedules', 'departure_date')->where('tour_id', $tourId),
            ],
            'available_slots' => ['required', 'integer', 'min:1'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'departure_date.required' => 'Vui lòng chọn ngày khởi hành.',
            'departure_date.date' => 'Ngày khởi hành không hợp lệ.',
            'departure_date.unique' => 'Ngày khởi hành này đã tồn tại trong lịch trình của Tour.',
            'available_slots.required' => 'Vui lòng nhập số chỗ khả dụng.',
            'available_slots.integer' => 'Số chỗ phải là số nguyên.',
            'available_slots.min' => 'Số chỗ khả dụng phải ít nhất là 1.',
            'price_override.numeric' => 'Giá khởi hành riêng phải là số.',
            'price_override.min' => 'Giá khởi hành riêng không được âm.',
        ];
    }
}
