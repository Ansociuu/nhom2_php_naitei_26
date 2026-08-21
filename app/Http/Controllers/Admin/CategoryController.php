<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::with(['parent'])->withCount(['tours', 'children']);

        if ($request->filled('search')) 
        {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderByDesc('category_id')->paginate(10)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::orderBy('name')->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục đã được tạo thành công.');
    }

    public function show(Category $category): View
    {
        $category->load(['parent', 'children']);
        $tours = $category->tours()->orderByDesc('tour_id')->paginate(10);

        return view('admin.categories.show', compact('category', 'tours'));
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::where('category_id', '!=', $category->category_id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) 
        {
            return redirect()->back()
                ->with('error', 'Không thể xóa danh mục này vì vẫn còn chứa các danh mục con.');
        }

        if ($category->tours()->exists()) 
        {
            return redirect()->back()
                ->with('error', 'Không thể xóa danh mục này vì đang chứa các Tour du lịch.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công.');
    }
}
