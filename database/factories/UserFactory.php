<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Mật khẩu mặc định của mọi user demo.
     */
    public const DEFAULT_PASSWORD = 'password';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            // cast 'hashed' trong model sẽ tự hash giá trị này
            'password_hash' => self::DEFAULT_PASSWORD,
            'role' => 'user',
            'status' => 'active',
            'last_login_at' => fake()->optional()->dateTimeBetween('-1 month'),
        ];
    }

    /**
     * User quản trị.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * User đã bị khoá.
     */
    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'banned',
        ]);
    }

    /**
     * User chưa kích hoạt.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * User chưa từng đăng nhập.
     */
    public function neverLoggedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_login_at' => null,
        ]);
    }
}
