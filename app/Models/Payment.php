<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'booking_id',
        'amount',
        'status',
        'gateway',
        'gateway_txn_id',
        'paid_at',
        'expire_at',
    ];

    protected $hidden = [
        'gateway_txn_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'created_at' => 'datetime',
            'paid_at' => 'datetime',
            'expire_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
