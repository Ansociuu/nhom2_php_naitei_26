<?php

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;

uses(RefreshDatabase::class);

test('social auth redirect returns target url for valid provider', function () {
    $response = $this->getJson('/api/v1/auth/social/google/redirect');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ])
        ->assertJsonStructure(['status', 'redirect_url']);
});

test('social auth redirect returns 422 for invalid provider', function () {
    $response = $this->getJson('/api/v1/auth/social/invalid_provider/redirect');

    $response->assertStatus(422)
        ->assertJson([
            'status'  => 'error',
            'message' => 'Nhà cung cấp đăng nhập mạng xã hội không hỗ trợ.',
        ]);
});

test('social auth callback creates new user and returns bearer token', function () {
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('google_123456');
    $abstractUser->shouldReceive('getName')->andReturn('Google User');
    $abstractUser->shouldReceive('getEmail')->andReturn('google_user@example.com');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->postJson('/api/v1/auth/social/google/callback');

    $response->assertStatus(200)
        ->assertJson([
            'status'  => 'success',
            'message' => 'Đăng nhập thành công.',
        ])
        ->assertJsonStructure(['access_token', 'user' => ['user_id', 'username', 'email']]);

    $this->assertDatabaseHas('users', [
        'email' => 'google_user@example.com',
    ]);

    $this->assertDatabaseHas('social_accounts', [
        'provider'         => 'google',
        'provider_user_id' => 'google_123456',
    ]);
});

test('social auth callback links to existing user email', function () {
    $user = User::factory()->create(['email' => 'existing@example.com']);

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('fb_987654');
    $abstractUser->shouldReceive('getName')->andReturn('Facebook User');
    $abstractUser->shouldReceive('getEmail')->andReturn('existing@example.com');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('facebook')->andReturn($provider);

    $response = $this->postJson('/api/v1/auth/social/facebook/callback');

    $response->assertStatus(200)
        ->assertJsonPath('user.email', 'existing@example.com');

    $this->assertDatabaseHas('social_accounts', [
        'user_id'          => $user->user_id,
        'provider'         => 'fb',
        'provider_user_id' => 'fb_987654',
    ]);
});
