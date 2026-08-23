<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    const CREATED_AT = 'booked_at';

    protected $table = 'bookings';

    protected $primaryKey = 'booking_id';

    public function getRouteKeyName(): string
    {
        return 'booking_id';
    }

    protected $fillable = [
        'user_id',
        'schedule_id',
        'ticket_type_id',
        'num_adults',
        'num_children',
        'unit_price',
        'total_amount',
        'note',
        'status',
        'booked_at',
        'confirmed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'num_adults' => 'integer',
            'num_children' => 'integer',
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'booked_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(TourSchedule::class, 'schedule_id', 'schedule_id');
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id', 'ticket_type_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'booking_id', 'booking_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(BookingDetail::class, 'booking_id', 'booking_id');
    }
}
