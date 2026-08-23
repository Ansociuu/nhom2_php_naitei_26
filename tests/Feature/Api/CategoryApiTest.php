<?php

use App\Models\Category;
use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list categories via API', function () {
    $parent = Category::factory()->create(['name' => 'Du lịch Miền Bắc', 'parent_id' => null]);
    Category::factory()->create(['name' => 'Hà Nội', 'parent_id' => $parent->category_id]);

    $response = $this->getJson('/api/v1/categories');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ])
        ->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'category_id',
                    'name',
                    'parent_id',
                    'children',
                ],
            ],
        ]);
});

test('can view single category with tours via API', function () {
    $category = Category::factory()->create(['name' => 'Biển đảo']);
    Tour::factory()->create([
        'category_id' => $category->category_id,
        'title'       => 'Tour Phú Quốc 3N2Đ',
        'status'      => 'active',
    ]);

    $response = $this->getJson("/api/v1/categories/{$category->category_id}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'category' => [
                'category_id' => $category->category_id,
                'name'        => 'Biển đảo',
            ],
        ])
        ->assertJsonPath('tours.data.0.title', 'Tour Phú Quốc 3N2Đ');
});
