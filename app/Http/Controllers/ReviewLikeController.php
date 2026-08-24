<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewLikeController extends Controller
{
    /**
     * Bật/tắt lượt thích cho một đánh giá, trả về JSON để cập nhật tại chỗ.
     */
    public function toggle(Request $request, Review $review): JsonResponse
    {
        abort_unless($review->status === 'approved', 404);

        $userId = $request->user()->user_id;
        $liked = $review->likes()->where('review_likes.user_id', $userId)->exists();

        if ($liked) {
            $review->likes()->detach($userId);
        } else {
            $review->likes()->attach($userId, ['liked_at' => now()]);
        }

        return response()->json([
            'liked' => ! $liked,
            'count' => $review->likes()->count(),
        ]);
    }
}
