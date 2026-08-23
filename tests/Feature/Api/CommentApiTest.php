<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot post a comment', function () {
    $this->postJson('/api/v1/reviews/1/comments', [
        'content' => 'Bình luận thử nghiệm',
    ])->assertStatus(401);
});

test('user cannot comment on an unapproved review', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    
    $review = Review::create([
        'user_id' => $user->user_id,
        'tour_id' => $tour->tour_id,
        'score'   => 5,
        'content' => 'Bài viết đang chờ duyệt',
        'status'  => 'pending',
    ]);

    $response = $this->actingAs($user)->postJson("/api/v1/reviews/{$review->review_id}/comments", [
        'content' => 'Bình luận thử nghiệm',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Bài đánh giá này chưa được duyệt hoặc không tồn tại.',
        ]);
});

test('user can comment and reply to a comment on an approved review via API', function () {
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $reviewer = User::factory()->create();
    $commenter = User::factory()->create();

    $review = Review::create([
        'user_id'     => $reviewer->user_id,
        'tour_id'     => $tour->tour_id,
        'score'       => 5,
        'content'     => 'Bài đánh giá công khai xuất sắc',
        'status'      => 'approved',
        'approved_at' => now(),
    ]);

    // Parent comment
    $response = $this->actingAs($commenter)->postJson("/api/v1/reviews/{$review->review_id}/comments", [
        'content' => 'Cảm ơn bài review rất chi tiết!',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'status'  => 'success',
            'message' => 'Thêm bình luận thành công.',
            'data'    => [
                'content' => 'Cảm ơn bài review rất chi tiết!',
            ],
        ]);

    $parentCommentId = $response->json('data.comment_id');

    // Reply comment
    $replyResponse = $this->actingAs($reviewer)->postJson("/api/v1/reviews/{$review->review_id}/comments", [
        'parent_comment_id' => $parentCommentId,
        'content'           => 'Rất vui vì thông tin giúp ích cho bạn!',
    ]);

    $replyResponse->assertStatus(201)
        ->assertJson([
            'status' => 'success',
            'data'   => [
                'parent_comment_id' => $parentCommentId,
                'content'           => 'Rất vui vì thông tin giúp ích cho bạn!',
            ],
        ]);

    $this->assertDatabaseHas('comments', [
        'review_id'         => $review->review_id,
        'user_id'           => $reviewer->user_id,
        'parent_comment_id' => $parentCommentId,
    ]);
});
