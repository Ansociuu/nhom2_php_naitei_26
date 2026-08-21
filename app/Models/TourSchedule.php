<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourSchedule extends Model
{
    use HasFactory;

    protected $table = 'tour_schedules';

    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'tour_id',
        'departure_date',
        'available_slots',
        'price_override',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'available_slots' => 'integer',
            'price_override' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'schedule_id', 'schedule_id');
    }
}
