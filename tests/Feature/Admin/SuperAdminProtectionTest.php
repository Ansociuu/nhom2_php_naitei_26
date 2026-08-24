<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');
});

test('admin can access create user page and store new user or admin account', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin/users/create');
    $response->assertStatus(200)->assertSee('Tạo tài khoản Người dùng / Admin mới');

    $storeResponse = $this->actingAs($admin)->post('/admin/users', [
        'username' => 'staffadmin',
        'email'    => 'staff@sunbooking.vn',
        'password' => 'password123',
        'role'     => 'admin',
        'status'   => 'active',
    ]);

    $storeResponse->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'username' => 'staffadmin',
        'email'    => 'staff@sunbooking.vn',
        'role'     => 'admin',
        'status'   => 'active',
    ]);
});

test('super admin protection prevents downgrading or deleting super admin', function () {
    $superAdmin = User::factory()->create([
        'user_id' => 1,
        'email'   => 'admin@sunbooking.vn',
        'role'    => 'admin',
        'status'  => 'active',
    ]);
    $superAdmin->assignRole('admin');

    $otherAdmin = User::factory()->create([
        'role'   => 'admin',
        'status' => 'active',
    ]);
    $otherAdmin->assignRole('admin');

    expect($superAdmin->isSuperAdmin())->toBeTrue();

    // Regular admin cannot downgrade super admin
    $response = $this->actingAs($otherAdmin)->patch("/admin/users/{$superAdmin->user_id}", [
        'role'   => 'user',
        'status' => 'banned',
    ]);

    $response->assertRedirect()->assertSessionHas('error', 'Không thể hạ cấp hoặc khóa tài khoản Super Admin tối cao.');

    // Regular admin cannot delete super admin
    $deleteResponse = $this->actingAs($otherAdmin)->delete("/admin/users/{$superAdmin->user_id}");
    $deleteResponse->assertRedirect()->assertSessionHas('error', 'Không thể xóa tài khoản Super Admin tối cao.');

    $this->assertDatabaseHas('users', [
        'user_id' => $superAdmin->user_id,
        'role'    => 'admin',
        'status'  => 'active',
    ]);
});
