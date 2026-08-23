<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Review\StoreCommentRequest;
use App\Http\Requests\Api\Review\StoreReviewRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Tour;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display completed bookings pending review and the user's existing reviews.
     */
    public function myReviews(Request $request): JsonResponse
    {
        $user = $request->user();

        $completedBookings = $user->bookings()
            ->with(['schedule.tour.images', 'ticketType'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('departure_date', '<', now()->toDateString()))
            ->whereIn('status', ['confirmed', 'completed'])
            ->latest('booked_at')
            ->get();

        $reviews = $user->reviews()
            ->with(['tour', 'images'])
            ->latest()
            ->get();

        $reviewedTourIds = $reviews->pluck('tour_id')->toArray();

        $pendingReviewBookings = $completedBookings->reject(
            fn ($b) => in_array($b->schedule->tour_id, $reviewedTourIds, true)
        )->values();

        return response()->json([
            'status'                            => 'success',
            'completed_bookings_pending_review' => $pendingReviewBookings->map(fn ($b) => [
                'booking_id'     => $b->booking_id,
                'tour_id'        => $b->schedule->tour_id,
                'tour_title'     => $b->schedule->tour?->title,
                'departure_date' => $b->schedule->departure_date->format('Y-m-d'),
            ]),
            'my_reviews'                        => ReviewResource::collection($reviews),
        ]);
    }

    /**
     * Create or update a review for a completed tour booking.
     */
    public function store(StoreReviewRequest $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== $request->user()->user_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bạn không có quyền đánh giá đơn đặt chỗ này.',
            ], 403);
        }

        if (! in_array($booking->status, ['confirmed', 'completed'], true)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Đơn đặt chỗ chưa được xác nhận hoàn thành.',
            ], 403);
        }

        $booking->loadMissing('schedule');

        if ($booking->schedule->departure_date->startOfDay()->gte(now()->startOfDay())) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Chuyến đi chưa kết thúc nên chưa thể đánh giá.',
            ], 403);
        }

        $validated = $request->validated();
        $tourId = $booking->schedule->tour_id;
        $userId = $request->user()->user_id;

        $files = $request->file('images', []);
        $uploadedResults = [];
        foreach ($files as $file) {
            $uploadedResults[] = $this->cloudinaryService->upload($file, 'reviews');
        }

        $review = DB::transaction(function () use ($tourId, $userId, $validated, $uploadedResults) {
            $review = Review::updateOrCreate(
                ['user_id' => $userId, 'tour_id' => $tourId],
                [
                    'score'       => $validated['score'],
                    'content'     => $validated['content'],
                    'status'      => 'pending',
                    'approved_at' => null,
                ]
            );

            $order = (int) $review->images()->max('display_order');

            foreach ($uploadedResults as $res) {
                $review->images()->create([
                    'cloudinary_public_id' => $res['cloudinary_public_id'],
                    'secure_url'           => $res['secure_url'],
                    'format'               => $res['format'],
                    'width'                => $res['width'],
                    'height'               => $res['height'],
                    'bytes'                => $res['bytes'],
                    'display_order'        => ++$order,
                ]);
            }

            return $review;
        });

        $review->load(['tour', 'images']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Cảm ơn bạn đã đánh giá! Bài viết sẽ hiển thị sau khi được duyệt.',
            'data'    => new ReviewResource($review),
        ], 201);
    }

    /**
     * Display approved reviews for a specific tour.
     */
    public function tourReviews(Request $request, Tour $tour): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);

        $reviews = $tour->reviews()
            ->where('status', 'approved')
            ->with([
                'user',
                'images',
                'likes',
                'comments' => fn ($q) => $q->whereNull('parent_comment_id')->with(['user', 'replies.user'])->latest(),
            ])
            ->withCount('likes')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => ReviewResource::collection($reviews)->response()->getData(true),
        ]);
    }


    /**
     * Toggle like/unlike on a review.
     */
    public function toggleLike(Request $request, Review $review): JsonResponse
    {
        if ($review->status !== 'approved') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bài đánh giá này chưa được duyệt hoặc không tồn tại.',
            ], 403);
        }

        $user = $request->user();
        $alreadyLiked = $review->likes()->where('review_likes.user_id', $user->user_id)->exists();

        if ($alreadyLiked) {
            $review->likes()->detach($user->user_id);
            $isLiked = false;
            $message = 'Đã bỏ thích bài đánh giá.';
        } else {
            $review->likes()->attach($user->user_id, ['liked_at' => now()]);
            $isLiked = true;
            $message = 'Đã thích bài đánh giá.';
        }

        $likesCount = $review->likes()->count();

        return response()->json([
            'status'      => 'success',
            'message'     => $message,
            'is_liked'    => $isLiked,
            'likes_count' => $likesCount,
        ]);
    }

    /**
     * Delete the authenticated user's own review.
     */
    public function destroy(Request $request, Review $review): JsonResponse
    {
        if ($review->user_id !== $request->user()->user_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bạn không có quyền xóa bài đánh giá này.',
            ], 403);
        }

        $review->loadMissing('images');

        foreach ($review->images as $image) {
            if (! empty($image->cloudinary_public_id)) {
                $this->cloudinaryService->delete($image->cloudinary_public_id);
            }
        }

        $review->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã xóa bài đánh giá của bạn.',
        ]);
    }
}
