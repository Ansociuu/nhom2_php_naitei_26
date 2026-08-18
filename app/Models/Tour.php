<?php

namespace App\Models;

use Database\Factories\TourFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tour extends Model
{
    /** @use HasFactory<TourFactory> */
    use HasFactory;

    protected $primaryKey = 'tour_id';
    protected $fillable = [
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
    ];
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
    public function itineraries(): HasMany
    {
        return $this->hasMany(TourItinerary::class, 'tour_id', 'tour_id');
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'tour_id', 'tour_id');
    }
}

