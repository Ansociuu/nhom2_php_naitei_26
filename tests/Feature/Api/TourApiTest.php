<?php

use App\Models\Category;
use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list active tours via API with pagination', function () {
    $category = Category::factory()->create();
    Tour::factory()->create([
        'category_id' => $category->category_id,
        'title'       => 'Tour Hà Giang mùa hoa',
        'status'      => 'active',
        'price'       => 3500000,
    ]);
    Tour::factory()->create([
        'category_id' => $category->category_id,
        'title'       => 'Tour Ẩn không hiển thị',
        'status'      => 'inactive',
    ]);

    $response = $this->getJson('/api/v1/tours');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ])
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.title', 'Tour Hà Giang mùa hoa');
});

test('can filter tours by keyword and price via API', function () {
    $category = Category::factory()->create();
    Tour::factory()->create([
        'category_id' => $category->category_id,
        'title'       => 'Tour Đà Nẵng Bà Nà Hill',
        'price'       => 2000000,
        'status'      => 'active',
    ]);
    Tour::factory()->create([
        'category_id' => $category->category_id,
        'title'       => 'Tour Nhat Rang',
        'price'       => 5000000,
        'status'      => 'active',
    ]);

    $response = $this->getJson('/api/v1/tours?keyword=Đà Nẵng&max_price=3000000');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.title', 'Tour Đà Nẵng Bà Nà Hill');
});

test('can view single active tour detail via API', function () {
    $category = Category::factory()->create();
    $tour = Tour::factory()->create([
        'category_id' => $category->category_id,
        'title'       => 'Tour Sapa Fansipan 2N1Đ',
        'status'      => 'active',
    ]);

    TourSchedule::factory()->create([
        'tour_id'         => $tour->tour_id,
        'departure_date'  => now()->addDays(5)->toDateString(),
        'available_slots' => 10,
    ]);

    $response = $this->getJson("/api/v1/tours/{$tour->tour_id}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data'   => [
                'tour_id' => $tour->tour_id,
                'title'   => 'Tour Sapa Fansipan 2N1Đ',
            ],
        ])
        ->assertJsonCount(1, 'data.schedules');
});

test('returns 404 when viewing inactive tour detail via API', function () {
    $category = Category::factory()->create();
    $tour = Tour::factory()->create([
        'category_id' => $category->category_id,
        'status'      => 'inactive',
    ]);

    $response = $this->getJson("/api/v1/tours/{$tour->tour_id}");

    $response->assertStatus(404)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Tour không tồn tại hoặc đã bị ẩn.',
        ]);
});

test('can get featured tours ordered by highest rating via API', function () {
    $category = Category::factory()->create();
    $tour1 = Tour::factory()->create(['category_id' => $category->category_id, 'title' => 'Tour Đánh giá Thấp', 'status' => 'active']);
    $tour2 = Tour::factory()->create(['category_id' => $category->category_id, 'title' => 'Tour Đánh giá Cao', 'status' => 'active']);

    // Add approved review with score 5 to tour2, score 3 to tour1
    \App\Models\Review::create([
        'user_id' => \App\Models\User::factory()->create()->user_id,
        'tour_id' => $tour2->tour_id,
        'score'   => 5,
        'content' => 'Rất tốt',
        'status'  => 'approved',
    ]);
    \App\Models\Review::create([
        'user_id' => \App\Models\User::factory()->create()->user_id,
        'tour_id' => $tour1->tour_id,
        'score'   => 3,
        'content' => 'Tạm ổn',
        'status'  => 'approved',
    ]);

    $response = $this->getJson('/api/v1/tours/featured?limit=2');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.title', 'Tour Đánh giá Cao');
});

test('can get distinct departure locations via API', function () {
    $category = Category::factory()->create();
    Tour::factory()->create(['category_id' => $category->category_id, 'departure_location' => 'Hà Nội', 'status' => 'active']);
    Tour::factory()->create(['category_id' => $category->category_id, 'departure_location' => 'Hà Nội', 'status' => 'active']);
    Tour::factory()->create(['category_id' => $category->category_id, 'departure_location' => 'Đà Nẵng', 'status' => 'active']);

    $response = $this->getJson('/api/v1/tours/locations');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data'   => ['Hà Nội', 'Đà Nẵng'],
        ]);
});
