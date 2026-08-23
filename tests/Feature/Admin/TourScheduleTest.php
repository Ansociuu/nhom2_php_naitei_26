<?php

use App\Models\Category;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');
});

test('guests cannot manage tour schedules', function () {
    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);

    $response = $this->post(route('admin.tours.schedules.store', $tour), [
        'departure_date' => now()->addDays(5)->format('Y-m-d'),
        'available_slots' => 20,
    ]);

    $response->assertRedirect(route('login'));
});

test('regular users cannot manage tour schedules', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);

    $response = $this->actingAs($user)->post(route('admin.tours.schedules.store', $tour), [
        'departure_date' => now()->addDays(5)->format('Y-m-d'),
        'available_slots' => 20,
    ]);

    $response->assertStatus(403);
});

test('admin can create a departure schedule for a tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $date = now()->addDays(10)->format('Y-m-d');

    $response = $this->actingAs($admin)->post(route('admin.tours.schedules.store', $tour), [
        'departure_date' => $date,
        'available_slots' => 25,
        'price_override' => 5000000,
    ]);

    $response->assertRedirect(route('admin.tours.show', $tour));

    $this->assertDatabaseHas('tour_schedules', [
        'tour_id' => $tour->tour_id,
        'departure_date' => $date,
        'available_slots' => 25,
        'price_override' => 5000000,
    ]);
});

test('admin can update a schedule', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $schedule = TourSchedule::factory()->create([
        'tour_id' => $tour->tour_id,
        'departure_date' => now()->addDays(5)->format('Y-m-d'),
        'available_slots' => 15,
    ]);

    $newDate = now()->addDays(15)->format('Y-m-d');

    $response = $this->actingAs($admin)->put(route('admin.tours.schedules.update', [$tour, $schedule]), [
        'departure_date' => $newDate,
        'available_slots' => 30,
        'price_override' => 4500000,
    ]);

    $response->assertRedirect(route('admin.tours.show', $tour));

    $this->assertDatabaseHas('tour_schedules', [
        'schedule_id' => $schedule->schedule_id,
        'departure_date' => $newDate,
        'available_slots' => 30,
        'price_override' => 4500000,
    ]);
});

test('admin can delete a schedule', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $schedule = TourSchedule::factory()->create(['tour_id' => $tour->tour_id]);

    $response = $this->actingAs($admin)->delete(route('admin.tours.schedules.destroy', [$tour, $schedule]));

    $response->assertRedirect(route('admin.tours.show', $tour));

    $this->assertDatabaseMissing('tour_schedules', [
        'schedule_id' => $schedule->schedule_id,
    ]);
});

test('duplicate departure date for same tour fails validation', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour = Tour::factory()->create(['category_id' => $category->category_id]);
    $date = now()->addDays(7)->format('Y-m-d');

    TourSchedule::factory()->create([
        'tour_id' => $tour->tour_id,
        'departure_date' => $date,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.tours.schedules.store', $tour), [
        'departure_date' => $date,
        'available_slots' => 20,
    ]);

    $response->assertSessionHasErrors(['departure_date']);
});

test('cannot update or delete schedule belonging to another tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create();
    $tour1 = Tour::factory()->create(['category_id' => $category->category_id]);
    $tour2 = Tour::factory()->create(['category_id' => $category->category_id]);

    $scheduleForTour2 = TourSchedule::factory()->create(['tour_id' => $tour2->tour_id]);

    $updateResponse = $this->actingAs($admin)->put(route('admin.tours.schedules.update', [$tour1, $scheduleForTour2]), [
        'departure_date' => now()->addDays(5)->format('Y-m-d'),
        'available_slots' => 10,
    ]);
    $updateResponse->assertStatus(404);

    $deleteResponse = $this->actingAs($admin)->delete(route('admin.tours.schedules.destroy', [$tour1, $scheduleForTour2]));
    $deleteResponse->assertStatus(404);
});
