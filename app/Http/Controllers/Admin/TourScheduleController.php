<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourScheduleRequest;
use App\Http\Requests\Admin\UpdateTourScheduleRequest;
use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Http\RedirectResponse;

class TourScheduleController extends Controller
{
    /**
     * Store a newly created departure schedule for a tour.
     */
    public function store(StoreTourScheduleRequest $request, Tour $tour): RedirectResponse
    {
        $tour->schedules()->create($request->validated());

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Thêm lịch khởi hành thành công.');
    }

    /**
     * Update the specified departure schedule.
     */
    public function update(UpdateTourScheduleRequest $request, Tour $tour, TourSchedule $schedule): RedirectResponse
    {
        if ($schedule->tour_id !== $tour->tour_id) {
            abort(404);
        }

        $schedule->update($request->validated());

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Cập nhật lịch khởi hành thành công.');
    }

    /**
     * Delete a departure schedule from a tour.
     */
    public function destroy(Tour $tour, TourSchedule $schedule): RedirectResponse
    {
        if ($schedule->tour_id !== $tour->tour_id) {
            abort(404);
        }

        $schedule->delete();

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Đã xóa lịch khởi hành.');
    }
}
