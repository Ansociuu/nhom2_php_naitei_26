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

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'user_id';
    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'role',
        'status',
        'last_login_at',
    ];
    protected $hidden = [
        'password_hash',
    ];
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

    //Tên cột password dùng cho Laravel Authentication vì Laravel nhận diện password không phải password_hash
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
    // Relationships
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class, 'user_id', 'user_id');
    }
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'user_id', 'user_id');
    }
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id', 'user_id');
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }
    public function reviewLikes(): BelongsToMany
    {
        return $this->belongsToMany(
            Review::class,
            'review_likes',
            'user_id',
            'review_id',
            'user_id',
            'review_id'
        )->withPivot('liked_at');
    }
}
