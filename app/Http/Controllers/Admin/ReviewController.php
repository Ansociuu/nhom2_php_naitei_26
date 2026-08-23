<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'tour', 'images'])
            ->withCount(['likes', 'comments', 'images']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('tour', function ($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('username', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->orderBy('review_id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load([
            'user',
            'tour.category',
            'images',
            'comments' => function ($q) {
                $q->whereNull('parent_comment_id')->with(['user', 'replies.user'])->orderBy('created_at', 'asc');
            },
        ]);
        $review->loadCount(['likes', 'comments', 'images']);

        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $review->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Đã duyệt đánh giá thành công.');
    }

    public function reject(Review $review)
    {
        $review->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Đã từ chối đánh giá thành công.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}
