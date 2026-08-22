<?php

use App\Models\Category;
use App\Models\Tour;
use App\Models\TourImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');
    Storage::fake('public');
});

test('admin can upload images for a tour and auto-sets first image as cover', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);

    $file1 = UploadedFile::fake()->create('tour1.jpg', 100, 'image/jpeg');
    $file2 = UploadedFile::fake()->create('tour2.png', 150, 'image/png');

    $response = $this->actingAs($admin)->post(route('admin.tours.images.store', $tour), [
        'images' => [$file1, $file2],
    ]);

    $response->assertRedirect(route('admin.tours.show', $tour));

    $this->assertDatabaseHas('tour_images', [
        'tour_id' => $tour->tour_id,
        'format' => 'jpg',
        'is_cover' => true,
    ]);

    $this->assertDatabaseHas('tour_images', [
        'tour_id' => $tour->tour_id,
        'format' => 'png',
        'is_cover' => false,
    ]);
});

test('admin can change cover image of a tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);

    $image1 = TourImage::factory()->cover()->create(['tour_id' => $tour->tour_id]);
    $image2 = TourImage::factory()->create(['tour_id' => $tour->tour_id, 'is_cover' => false]);

    $response = $this->actingAs($admin)->patch(route('admin.tours.images.cover', [$tour, $image2]));

    $response->assertRedirect(route('admin.tours.show', $tour));

    expect($image1->fresh()->is_cover)->toBeFalse();
    expect($image2->fresh()->is_cover)->toBeTrue();
});

test('admin can delete an image and cover automatically transfers if cover was deleted', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);

    $coverImage = TourImage::factory()->cover()->create(['tour_id' => $tour->tour_id, 'display_order' => 1]);
    $secondImage = TourImage::factory()->create(['tour_id' => $tour->tour_id, 'is_cover' => false, 'display_order' => 2]);

    $response = $this->actingAs($admin)->delete(route('admin.tours.images.destroy', [$tour, $coverImage]));

    $response->assertRedirect(route('admin.tours.show', $tour));

    $this->assertDatabaseMissing('tour_images', [
        'image_id' => $coverImage->image_id,
    ]);

    expect($secondImage->fresh()->is_cover)->toBeTrue();
});

test('regular users cannot manage tour images', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($user)->post(route('admin.tours.images.store', $tour), [
        'images' => [$file],
    ]);

    $response->assertStatus(403);
});
