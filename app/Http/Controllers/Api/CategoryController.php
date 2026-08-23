<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TourResource;
use App\Models\Category;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories (Root categories with children & active tours count).
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->withCount('activeTours');
            }])
            ->withCount('activeTours')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Display the specified category with its active tours.
     */
    public function show(Category $category, Request $request): JsonResponse
    {
        $category->loadCount('activeTours');
        $category->load(['parent', 'children']);

        // Collect current category ID and all child category IDs
        $categoryIds = array_merge([$category->category_id], $category->children->pluck('category_id')->toArray());

        $perPage = (int) $request->input('per_page', 15);
        $tours = Tour::whereIn('category_id', $categoryIds)
            ->where('status', 'active')
            ->with(['category', 'images' => function ($query) {
                $query->where('is_cover', true);
            }])
            ->withAvg(['reviews' => function ($query) {
                $query->where('status', 'approved');
            }], 'score')
            ->withCount(['reviews' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->orderByDesc('tour_id')
            ->paginate($perPage);

        return response()->json([
            'status'   => 'success',
            'category' => new CategoryResource($category),
            'tours'    => TourResource::collection($tours)->response()->getData(true),
        ]);
    }
}
