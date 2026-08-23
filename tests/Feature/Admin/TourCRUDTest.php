<?php

use App\Models\Category;
use App\Models\Tour;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');
});

test('guests cannot access tour management', function () {
    $response = $this->get(route('admin.tours.index'));
    $response->assertRedirect(route('login'));
});

test('regular users cannot access tour management', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $response = $this->actingAs($user)->get(route('admin.tours.index'));
    $response->assertStatus(403);
});

test('admin can view tour list', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create([
        'category_id' => $category->category_id,
        'title' => 'Tour Khám Phá Sapa 3N2Đ',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.tours.index'));
    $response->assertStatus(200);
    $response->assertSee('Tour Khám Phá Sapa 3N2Đ');
});

test('admin can create a tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.tours.store'), [
        'category_id' => $category->category_id,
        'title' => 'Tour Hạ Long 2N1Đ Sang Trọng',
        'price' => 2500000,
        'duration_days' => 2,
        'departure_location' => 'Hà Nội',
        'description' => 'Mô tả chi tiết tour Hạ Long.',
        'status' => 'active',
    ]);

    $this->assertDatabaseHas('tours', [
        'title' => 'Tour Hạ Long 2N1Đ Sang Trọng',
        'price' => 2500000,
    ]);

    $tour = Tour::where('title', 'Tour Hạ Long 2N1Đ Sang Trọng')->first();
    $response->assertRedirect(route('admin.tours.show', $tour));
});

test('admin can view tour details', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create([
        'category_id' => $category->category_id,
        'title' => 'Tour Chi Tiết Đà Nẵng - Hội An',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.tours.show', $tour));
    $response->assertStatus(200);
    $response->assertSee('Tour Chi Tiết Đà Nẵng - Hội An');
});

test('admin can update a tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create([
        'category_id' => $category->category_id,
        'title' => 'Tour Cũ',
        'price' => 1000000,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.tours.update', $tour), [
        'category_id' => $category->category_id,
        'title' => 'Tour Đã Cập Nhật',
        'price' => 1500000,
        'duration_days' => 3,
        'status' => 'active',
    ]);

    $response->assertRedirect(route('admin.tours.show', $tour));
    $this->assertDatabaseHas('tours', [
        'tour_id' => $tour->tour_id,
        'title' => 'Tour Đã Cập Nhật',
        'price' => 1500000,
    ]);
});

test('admin can delete a tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create([
        'category_id' => $category->category_id,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.tours.destroy', $tour));

    $response->assertRedirect(route('admin.tours.index'));
    $this->assertDatabaseMissing('tours', [
        'tour_id' => $tour->tour_id,
    ]);
});
