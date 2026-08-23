<?php

namespace App\Models;

use Database\Factories\TourFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Tour extends Model
{
    /** @use HasFactory<TourFactory> */
    use HasFactory;

    protected $table = 'tours';

    protected $primaryKey = 'tour_id';

    public function getRouteKeyName(): string
    {
        return 'tour_id';
    }

    protected $fillable = [
        'category_id',
        'region',
        'province',
        'title',
        'description',
        'highlights',
        'departure_location',
        'price',
        'duration_days',
        'duration_label',
        'difficulty',
        'peak_elevation',
        'elevation_gain',
        'distance_km',
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
            'difficulty' => 'integer',
            'peak_elevation' => 'integer',
            'elevation_gain' => 'integer',
            'distance_km' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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

    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class, 'tour_id', 'tour_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(TourImage::class, 'tour_id', 'tour_id');
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class, 'tour_id', 'tour_id');
    }

    public function coverImageUrl(): ?string
    {
        return ($this->images->firstWhere('is_cover', true) ?? $this->images->first())?->url();
    }

    public function cheapestTicketType(): ?TicketType
    {
        return $this->ticketTypes->sortBy('price')->first();
    }

    public function difficultyLabel(): ?string
    {
        return match ($this->difficulty) {
            1 => 'Rất dễ',
            2 => 'Dễ',
            3 => 'Trung bình',
            4 => 'Khó',
            5 => 'Rất khó',
            default => null,
        };
    }

    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(
            Booking::class,
            TourSchedule::class,
            'tour_id', 
            'schedule_id',
            'tour_id', 
            'schedule_id'
        );
    }
}
