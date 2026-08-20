<?php

use App\Models\Category;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');
});

test('guests cannot access category management', function () {
    $response = $this->get(route('admin.categories.index'));
    $response->assertRedirect(route('login'));
});

test('regular users cannot access category management', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $response = $this->actingAs($user)->get(route('admin.categories.index'));
    $response->assertStatus(403);
});

test('admin can view category list', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Category::factory()->create(['name' => 'Du lịch Miền Bắc']);

    $response = $this->actingAs($admin)->get(route('admin.categories.index'));
    $response->assertStatus(200);
    $response->assertSee('Du lịch Miền Bắc');
});

test('admin can create a category', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
        'name' => 'Du lịch Miền Nam',
        'parent_id' => null,
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $this->assertDatabaseHas('categories', [
        'name' => 'Du lịch Miền Nam',
    ]);
});

test('admin can view category details', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Du lịch Miền Trung']);

    $response = $this->actingAs($admin)->get(route('admin.categories.show', $category));
    $response->assertStatus(200);
    $response->assertSee('Du lịch Miền Trung');
});

test('admin can update a category', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Du lịch Cũ']);

    $response = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
        'name' => 'Du lịch Mới',
        'parent_id' => null,
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $this->assertDatabaseHas('categories', [
        'category_id' => $category->category_id,
        'name' => 'Du lịch Mới',
    ]);
});

test('admin can delete an empty category', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Danh mục Cần Xóa']);

    $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect(route('admin.categories.index'));
    $this->assertDatabaseMissing('categories', [
        'category_id' => $category->category_id,
    ]);
});
