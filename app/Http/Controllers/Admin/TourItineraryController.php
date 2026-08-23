<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourItineraryRequest;
use App\Http\Requests\Admin\UpdateTourItineraryRequest;
use App\Models\Tour;
use App\Models\TourItinerary;
use Illuminate\Http\RedirectResponse;

class TourItineraryController extends Controller
{
    /**
     * Store a newly created itinerary day for a tour.
     */
    public function store(StoreTourItineraryRequest $request, Tour $tour): RedirectResponse
    {
        $tour->itineraries()->create($request->validated());

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Thêm ngày lịch trình thành công.');
    }

    /**
     * Update the specified itinerary day.
     */
    public function update(UpdateTourItineraryRequest $request, Tour $tour, TourItinerary $itinerary): RedirectResponse
    {
        if ($itinerary->tour_id !== $tour->tour_id) {
            abort(404);
        }

        $itinerary->update($request->validated());

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Cập nhật lịch trình thành công.');
    }

    /**
     * Delete an itinerary day from a tour.
     */
    public function destroy(Tour $tour, TourItinerary $itinerary): RedirectResponse
    {
        if ($itinerary->tour_id !== $tour->tour_id) {
            abort(404);
        }

        $itinerary->delete();

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Đã xóa ngày lịch trình khỏi Tour.');
    }
}
