<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPassenger extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'booking_passengers';

    protected $primaryKey = 'passenger_id';

    protected $fillable = [
        'booking_id',
        'full_name',
        'age',
        'phone',
        'seat_no',
        'is_booker',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'is_booker' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
