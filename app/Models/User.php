<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['username', 'email', 'password_hash', 'role', 'status', 'last_login_at'])]
#[Hidden(['password_hash'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    /**
     * Bảng users không có cột remember_token nên tắt tính năng "remember me".
     */
    protected $rememberTokenName = '';

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}