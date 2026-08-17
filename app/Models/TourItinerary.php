<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourItinerary extends Model
{
    use HasFactory;

    protected $table = 'tour_itineraries';

    protected $primaryKey = 'itinerary_id';

    protected $fillable = [
        'tour_id',
        'day_number',
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'day_number' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
}
