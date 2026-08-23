<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Review\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Add a comment or reply to an approved review.
     */
    public function store(StoreCommentRequest $request, Review $review): JsonResponse
    {
        if ($review->status !== 'approved') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bài đánh giá này chưa được duyệt hoặc không tồn tại.',
            ], 403);
        }

        $validated = $request->validated();

        $comment = $review->comments()->create([
            'user_id'           => $request->user()->user_id,
            'parent_comment_id' => $validated['parent_comment_id'] ?? null,
            'content'           => $validated['content'],
        ]);

        $comment->load(['user', 'replies.user']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Thêm bình luận thành công.',
            'data'    => new CommentResource($comment),
        ], 201);
    }
}
