<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourRequest;
use App\Http\Requests\Admin\UpdateTourRequest;
use App\Models\Category;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tour::with(['category']);

        if ($request->filled('search')) 
        {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('departure_location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) 
        {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) 
        {
            $query->where('status', $request->input('status'));
        }

        $tours = $query->orderByDesc('tour_id')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.tours.index', compact('tours', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.tours.create', compact('categories'));
    }

    public function store(StoreTourRequest $request): RedirectResponse
    {
        $tour = Tour::create($request->validated());

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Tạo Tour du lịch thành công.');
    }

    public function show(Tour $tour): View
    {
        $tour->load(['category', 'images', 'itineraries', 'schedules', 'reviews']);

        return view('admin.tours.show', compact('tour'));
    }

    public function edit(Tour $tour): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.tours.edit', compact('tour', 'categories'));
    }

    public function update(UpdateTourRequest $request, Tour $tour): RedirectResponse
    {
        $tour->update($request->validated());

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Cập nhật Tour du lịch thành công.');
    }

    public function destroy(Tour $tour): RedirectResponse
    {
        DB::transaction(function () use ($tour) {
            $tour->itineraries()->delete();
            $tour->images()->delete();
            $tour->schedules()->delete();
            $tour->delete();
        });

        return redirect()->route('admin.tours.index')
            ->with('success', 'Xóa Tour du lịch thành công.');
    }
}
