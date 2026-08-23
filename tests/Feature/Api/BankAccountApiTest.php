<?php

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access bank account endpoints', function () {
    $this->getJson('/api/v1/auth/bank-account')->assertStatus(401);
    $this->putJson('/api/v1/auth/bank-account', [
        'bank_name'           => 'Vietcombank',
        'account_number'      => '123456789',
        'account_holder_name' => 'NGUYEN VAN A',
    ])->assertStatus(401);
});

test('user can get null bank account when none exists', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/auth/bank-account');

    $response->assertStatus(200)
        ->assertJson([
            'status'       => 'success',
            'bank_account' => null,
        ]);
});

test('user can create or update bank account via API', function () {
    $user = User::factory()->create();

    $payload = [
        'bank_name'           => 'Vietcombank',
        'account_number'      => '9876543210',
        'account_holder_name' => 'nguyen van a',
    ];

    $response = $this->actingAs($user)->putJson('/api/v1/auth/bank-account', $payload);

    $response->assertStatus(200)
        ->assertJson([
            'status'       => 'success',
            'message'      => 'Cập nhật tài khoản ngân hàng thành công.',
            'bank_account' => [
                'bank_name'           => 'Vietcombank',
                'account_number'      => '9876543210',
                'account_holder_name' => 'NGUYEN VAN A', // Auto uppercase
            ],
        ]);

    $this->assertDatabaseHas('bank_accounts', [
        'user_id'             => $user->user_id,
        'bank_name'           => 'Vietcombank',
        'account_number'      => '9876543210',
        'account_holder_name' => 'NGUYEN VAN A',
    ]);
});

test('user can get existing bank account via API', function () {
    $user = User::factory()->create();
    BankAccount::create([
        'user_id'             => $user->user_id,
        'bank_name'           => 'MB Bank',
        'account_number'      => '0901234567',
        'account_holder_name' => 'TRAN VAN B',
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/auth/bank-account');

    $response->assertStatus(200)
        ->assertJson([
            'status'       => 'success',
            'bank_account' => [
                'bank_name'           => 'MB Bank',
                'account_number'      => '0901234567',
                'account_holder_name' => 'TRAN VAN B',
            ],
        ]);
});
