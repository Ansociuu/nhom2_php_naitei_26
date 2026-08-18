<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'admin@sunbooking.test';

    /**
     * Số user thường được tạo cho môi trường demo.
     */
    public const DEMO_USERS = 10;

    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => self::ADMIN_EMAIL],
            UserFactory::new()->admin()->raw([
                'username' => 'admin',
                'email' => self::ADMIN_EMAIL,
            ])
        );
        $admin->syncRoles('admin');

        // vài user có trạng thái đặc biệt để test màn hình quản lý user
        $users = User::factory()->count(self::DEMO_USERS - 2)->create()
            ->concat(User::factory()->inactive()->neverLoggedIn()->count(1)->create())
            ->concat(User::factory()->banned()->count(1)->create());

        foreach ($users as $user) {
            $user->syncRoles('user');
        }
    }
}
