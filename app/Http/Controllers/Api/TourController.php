<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourDetailResource;
use App\Http\Resources\TourResource;
use App\Models\Category;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Display a listing of active tours with search, filtering, sorting & pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tour::query()
            ->where('status', 'active')
            ->with(['category', 'images' => function ($q) {
                $q->orderBy('display_order');
            }])
            ->withAvg(['reviews' => function ($q) {
                $q->where('status', 'approved');
            }], 'score')
            ->withCount(['reviews' => function ($q) {
                $q->where('status', 'approved');
            }]);

        // Search by keyword
        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('departure_location', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filter by category_id (including children)
        if ($categoryId = $request->input('category_id')) {
            $category = Category::with('children')->find($categoryId);
            if ($category) {
                $categoryIds = array_merge([$category->category_id], $category->children->pluck('category_id')->toArray());
                $query->whereIn('category_id', $categoryIds);
            } else {
                $query->where('category_id', $categoryId);
            }
        }

        // Filter by departure location
        if ($departureLocation = $request->input('departure_location')) {
            $query->where('departure_location', 'like', "%{$departureLocation}%");
        }

        // Filter by min & max price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        // Filter by duration_days
        if ($durationDays = $request->input('duration_days')) {
            $query->where('duration_days', (int) $durationDays);
        }

        // Filter tours that have available departure schedules
        if ($request->boolean('available_schedules_only')) {
            $query->whereHas('schedules', function ($q) {
                $q->where('departure_date', '>=', now()->toDateString())
                  ->where('available_slots', '>', 0);
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating_desc':
                $query->orderByDesc('reviews_avg_score');
                break;
            case 'latest':
            default:
                $query->orderByDesc('tour_id');
                break;
        }

        $perPage = (int) $request->input('per_page', 15);
        $tours = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => TourResource::collection($tours)->response()->getData(true),
        ]);
    }

    /**
     * Display the specified active tour detail.
     */
    public function show(Tour $tour): JsonResponse
    {
        if ($tour->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tour không tồn tại hoặc đã bị ẩn.',
            ], 404);
        }

        $tour->load([
            'category',
            'images' => fn ($q) => $q->orderBy('display_order'),
            'itineraries' => fn ($q) => $q->orderBy('day_number'),
            'schedules' => fn ($q) => $q->where('departure_date', '>=', now()->toDateString())
                                      ->where('available_slots', '>', 0)
                                      ->orderBy('departure_date'),
            'reviews' => fn ($q) => $q->where('status', 'approved')
                                    ->with('user')
                                    ->orderByDesc('review_id'),
        ]);

        $tour->loadAvg(['reviews' => fn ($q) => $q->where('status', 'approved')], 'score');
        $tour->loadCount(['reviews' => fn ($q) => $q->where('status', 'approved')]);

        return response()->json([
            'status' => 'success',
            'data'   => new TourDetailResource($tour),
        ]);
    }

    /**
     * Display a listing of featured active tours based on highest rating score.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 6);

        $tours = Tour::where('status', 'active')
            ->with(['category', 'images' => function ($q) {
                $q->orderBy('display_order');
            }])
            ->withAvg(['reviews' => function ($q) {
                $q->where('status', 'approved');
            }], 'score')
            ->withCount(['reviews' => function ($q) {
                $q->where('status', 'approved');
            }])
            ->orderByDesc('reviews_avg_score')
            ->orderByDesc('tour_id')
            ->take($limit)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => TourResource::collection($tours),
        ]);
    }

    /**
     * Display a distinct list of departure locations from active tours.
     */
    public function locations(): JsonResponse
    {
        $locations = Tour::where('status', 'active')
            ->whereNotNull('departure_location')
            ->where('departure_location', '!=', '')
            ->distinct()
            ->pluck('departure_location')
            ->values();

        return response()->json([
            'status' => 'success',
            'data'   => $locations,
        ]);
    }
}
