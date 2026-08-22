<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use HasFactory;

    protected $table = 'ticket_types';

    protected $primaryKey = 'ticket_type_id';

    protected $fillable = [
        'tour_id',
        'name',
        'price',
        'original_price',
        'target_audience',
        'features',
        'description',
        'included_services',
        'excluded_services',
        'includes_bus',
        'is_recommended',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'features' => 'array',
            'includes_bus' => 'boolean',
            'is_recommended' => 'boolean',
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
        return $this->hasMany(Booking::class, 'ticket_type_id', 'ticket_type_id');
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(TicketTypeHighlight::class, 'ticket_type_id', 'ticket_type_id');
    }
}
