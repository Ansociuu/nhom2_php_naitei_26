<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $primaryKey = 'review_id';

    public function getRouteKeyName(): string
    {
        return 'review_id';
    }

    protected $fillable = [
        'user_id',
        'tour_id',
        'content',
        'score',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class, 'review_id', 'review_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'review_id', 'review_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'review_likes',
            'review_id',
            'user_id',
            'review_id',
            'user_id'
        )->withPivot('liked_at');
    }
}
