<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'user']);
});

test('user can register via API', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'username'              => 'newuser',
        'email'                 => 'newuser@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'status' => 'success',
            'user'   => [
                'username' => 'newuser',
                'email'    => 'newuser@example.com',
            ],
        ])
        ->assertJsonStructure(['access_token', 'token_type']);

    $this->assertDatabaseHas('users', [
        'username' => 'newuser',
        'email'    => 'newuser@example.com',
    ]);
});

test('user can login via API with valid credentials', function () {
    $user = User::factory()->create([
        'username'      => 'testuser',
        'email'         => 'test@example.com',
        'password_hash' => Hash::make('password123'),
        'status'        => 'active',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'login'    => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'user'   => [
                'user_id' => $user->user_id,
            ],
        ])
        ->assertJsonStructure(['access_token', 'token_type']);
});

test('banned user cannot login via API', function () {
    User::factory()->create([
        'username'      => 'banneduser',
        'email'         => 'banned@example.com',
        'password_hash' => Hash::make('password123'),
        'status'        => 'banned',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'login'    => 'banned@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Tài khoản của bạn đang bị tạm khóa hoặc ngừng hoạt động.',
        ]);
});

test('authenticated user can get profile via API', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/auth/me');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'user'   => [
                'user_id' => $user->user_id,
                'email'   => $user->email,
            ],
        ]);
});

test('authenticated user can update profile via API', function () {
    $user = User::factory()->create(['username' => 'oldname', 'email' => 'old@example.com']);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/v1/auth/profile', [
            'username' => 'newname',
            'email'    => 'new@example.com',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'user'   => [
                'username' => 'newname',
                'email'    => 'new@example.com',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'user_id'  => $user->user_id,
        'username' => 'newname',
        'email'    => 'new@example.com',
    ]);
});

test('authenticated user can change password via API', function () {
    $user = User::factory()->create(['password_hash' => Hash::make('oldpassword')]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/v1/auth/password', [
            'current_password'      => 'oldpassword',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertStatus(200)
        ->assertJson(['status' => 'success']);

    $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password_hash));
});

test('authenticated user can logout via API', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/v1/auth/logout');

    $response->assertStatus(200)
        ->assertJson(['status' => 'success']);

    $this->assertCount(0, $user->tokens);
});
