<?php

use App\Models\Category;
use App\Models\Tour;
use App\Models\TourItinerary;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');
});

test('guests cannot manage tour itineraries', function () {
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);

    $response = $this->post(route('admin.tours.itineraries.store', $tour), [
        'day_number' => 1,
        'title' => 'Ngày 1: Tham quan',
    ]);

    $response->assertRedirect(route('login'));
});

test('regular users cannot manage tour itineraries', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);

    $response = $this->actingAs($user)->post(route('admin.tours.itineraries.store', $tour), [
        'day_number' => 1,
        'title' => 'Ngày 1: Tham quan',
    ]);

    $response->assertStatus(403);
});

test('admin can create an itinerary for a tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);

    $response = $this->actingAs($admin)->post(route('admin.tours.itineraries.store', $tour), [
        'day_number' => 1,
        'title' => 'Ngày 1: Hà Nội - Sapa',
        'description' => 'Xe đón đoàn tại điểm hẹn, khởi hành đi Sapa.',
    ]);

    $response->assertRedirect(route('admin.tours.show', $tour));

    $this->assertDatabaseHas('tour_itineraries', [
        'tour_id' => $tour->tour_id,
        'day_number' => 1,
        'title' => 'Ngày 1: Hà Nội - Sapa',
        'description' => 'Xe đón đoàn tại điểm hẹn, khởi hành đi Sapa.',
    ]);
});

test('admin can update an itinerary', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $itinerary = TourItinerary::factory()->create([
        'tour_id' => $tour->tour_id,
        'day_number' => 1,
        'title' => 'Tiêu đề cũ',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.tours.itineraries.update', [$tour, $itinerary]), [
        'day_number' => 1,
        'title' => 'Tiêu đề mới đã cập nhật',
        'description' => 'Mô tả chi tiết mới.',
    ]);

    $response->assertRedirect(route('admin.tours.show', $tour));

    $this->assertDatabaseHas('tour_itineraries', [
        'itinerary_id' => $itinerary->itinerary_id,
        'title' => 'Tiêu đề mới đã cập nhật',
        'description' => 'Mô tả chi tiết mới.',
    ]);
});

test('admin can delete an itinerary', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $itinerary = TourItinerary::factory()->create(['tour_id' => $tour->tour_id]);

    $response = $this->actingAs($admin)->delete(route('admin.tours.itineraries.destroy', [$tour, $itinerary]));

    $response->assertRedirect(route('admin.tours.show', $tour));

    $this->assertDatabaseMissing('tour_itineraries', [
        'itinerary_id' => $itinerary->itinerary_id,
    ]);
});

test('validation rules are enforced for tour itineraries', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);

    $response = $this->actingAs($admin)->post(route('admin.tours.itineraries.store', $tour), [
        'day_number' => '',
        'title' => '',
    ]);

    $response->assertSessionHasErrors(['day_number', 'title']);
});

test('cannot update or delete itinerary belonging to another tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour1 = Tour::factory()->create(['category_id' => $category->category_id]);
    $tour2 = Tour::factory()->create(['category_id' => $category->category_id]);

    $itineraryForTour2 = TourItinerary::factory()->create(['tour_id' => $tour2->tour_id]);

    $updateResponse = $this->actingAs($admin)->put(route('admin.tours.itineraries.update', [$tour1, $itineraryForTour2]), [
        'day_number' => 1,
        'title' => 'Hack title',
    ]);
    $updateResponse->assertStatus(404);

    $deleteResponse = $this->actingAs($admin)->delete(route('admin.tours.itineraries.destroy', [$tour1, $itineraryForTour2]));
    $deleteResponse->assertStatus(404);
});
