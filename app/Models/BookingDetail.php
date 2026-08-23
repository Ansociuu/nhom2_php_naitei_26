<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDetail extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'booking_details';

    protected $primaryKey = 'booking_detail_id';

    public function getRouteKeyName(): string
    {
        return 'booking_detail_id';
    }

    protected $fillable = [
        'booking_id',
        'name',
        'age',
        'price',
        'phone',
        'seat_no',
        'is_booker',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'price' => 'decimal:2',
            'is_booker' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
