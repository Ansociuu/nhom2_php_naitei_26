<?php

namespace App\Models;

use Database\Factories\TourFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'title',
    'description',
    'highlights',
    'departure_location',
    'price',
    'duration_days',
    'included_services',
    'excluded_services',
    'status',
])]
class Tour extends Model
{
    /** @use HasFactory<TourFactory> */
    use HasFactory;

    protected $table = 'tours';

    protected $primaryKey = 'tour_id';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class, 'tour_id', 'tour_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(TourImage::class, 'tour_id', 'tour_id');
    }
}