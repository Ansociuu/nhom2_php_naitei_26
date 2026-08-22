<?php

namespace App\Http\Requests;

use App\Models\TourSchedule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'schedule_id'          => ['required', 'integer', 'exists:tour_schedules,schedule_id'],
            'passengers'           => ['required', 'array', 'min:1'],
            'passengers.*.name'    => ['required', 'string', 'max:255'],
            'passengers.*.age'     => ['required', 'integer', 'min:0', 'max:120'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $schedule = TourSchedule::with('tour')->find($this->schedule_id);

            if (! $schedule) {
                $validator->errors()->add('schedule_id', 'Lịch khởi hành không tồn tại.');
                return;
            }

            // Tour must be active
            if ($schedule->tour->status !== 'active') {
                $validator->errors()->add('schedule_id', 'Tour này hiện không khả dụng.');
                return;
            }

            // Departure must be in the future
            if ($schedule->departure_date->isPast() || $schedule->departure_date->isToday()) {
                $validator->errors()->add('schedule_id', 'Lịch khởi hành phải là ngày trong tương lai.');
                return;
            }

            // Check available slots against passenger count
            $passengers = $this->input('passengers', []);
            $totalGuests = count($passengers);

            if ($totalGuests > $schedule->available_slots) {
                $validator->errors()->add(
                    'passengers',
                    "Chỉ còn {$schedule->available_slots} chỗ trống. Bạn đã đăng ký {$totalGuests} người."
                );
                return;
            }

            // Must have at least 1 adult (age >= 12)
            $hasAdult = collect($passengers)->contains(fn ($p) => isset($p['age']) && intval($p['age']) >= 12);
            if (! $hasAdult) {
                $validator->errors()->add(
                    'passengers',
                    'Đoàn phải có ít nhất 1 người lớn (từ 12 tuổi trở lên).'
                );
            }
        });
    }

    /**
     * Custom error messages in Vietnamese.
     */
    public function messages(): array
    {
        return [
            'schedule_id.required'       => 'Vui lòng chọn lịch khởi hành.',
            'schedule_id.exists'         => 'Lịch khởi hành không hợp lệ.',
            'passengers.required'        => 'Vui lòng nhập danh sách hành khách.',
            'passengers.min'             => 'Phải có ít nhất 1 người tham gia.',
            'passengers.*.name.required' => 'Họ và tên hành khách không được để trống.',
            'passengers.*.name.max'      => 'Họ và tên hành khách tối đa 255 ký tự.',
            'passengers.*.age.required'  => 'Tuổi hành khách không được để trống.',
            'passengers.*.age.integer'   => 'Tuổi hành khách phải là số nguyên.',
            'passengers.*.age.min'       => 'Tuổi không được âm.',
            'passengers.*.age.max'       => 'Tuổi không hợp lệ (tối đa 120).',
        ];
    }
}
