<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(Request $request): View
    {
        $tours = Tour::query()
            ->where('status', 'active')
            ->with(['images', 'ticketTypes'])
            ->when($request->filled('region'), fn ($query) => $query->where('region', $request->string('region')))
            ->when($request->filled('province'), fn ($query) => $query->where('province', $request->string('province')))
            ->when($request->filled('difficulty'), fn ($query) => $query->where('difficulty', $request->integer('difficulty')))
            ->orderBy('title')
            ->paginate(9)
            ->withQueryString();

        $provinces = Tour::query()
            ->where('status', 'active')
            ->whereNotNull('province')
            ->orderBy('province')
            ->distinct()
            ->pluck('province');

        return view('tours.index', [
            'tours' => $tours,
            'provinces' => $provinces,
        ]);
    }

    public function show(Tour $tour): View
    {
        $tour->load([
            'images',
            'itineraries' => fn ($query) => $query->orderBy('day_number'),
            'ticketTypes.highlights',
            'schedules' => fn ($query) => $query->where('departure_date', '>=', now()->toDateString())->orderBy('departure_date'),
            'reviews' => fn ($query) => $query->where('status', 'approved')->with(['user', 'images'])->latest(),
        ]);

        return view('tours.show', ['tour' => $tour]);
    }
}
