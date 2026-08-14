<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'social_accounts';

    protected $primaryKey = 'social_id';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'linked_at',
    ];

    protected $hidden = [
        'provider_user_id',
    ];

    protected function casts(): array
    {
        return [
            'linked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
