<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketTypeHighlight extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'ticket_type_highlights';

    protected $primaryKey = 'highlight_id';

    protected $fillable = [
        'ticket_type_id',
        'image_url',
        'title',
        'description',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id', 'ticket_type_id');
    }
}
