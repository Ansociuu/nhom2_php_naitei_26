<?php

namespace App\Http\Requests\Api\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'schedule_id'            => ['required', 'integer', 'exists:tour_schedules,schedule_id'],
            'ticket_type_id'         => ['required', 'integer', 'exists:ticket_types,ticket_type_id'],
            'note'                   => ['nullable', 'string', 'max:1000'],
            'passengers'             => ['required', 'array', 'min:1'],
            'passengers.*.full_name' => ['required', 'string', 'max:255'],
            'passengers.*.age'       => ['nullable', 'integer', 'min:0', 'max:120'],
            'passengers.*.phone'     => ['nullable', 'string', 'max:30'],
            'passengers.*.seat_no'   => ['nullable', 'string', 'max:20'],
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
            'schedule_id'            => 'lịch khởi hành',
            'ticket_type_id'         => 'loại vé',
            'note'                   => 'Ghi chú',
            'passengers'             => 'danh sách hành khách',
            'passengers.*.full_name' => 'họ tên hành khách',
            'passengers.*.age'       => 'tuổi',
            'passengers.*.phone'     => 'số điện thoại',
            'passengers.*.seat_no'   => 'số ghế',
        ];
    }
}
