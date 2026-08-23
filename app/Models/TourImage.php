<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourImage extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'tour_images';

    protected $primaryKey = 'image_id';

    protected $fillable = [
        'tour_id',
        'image_url',
        'cloudinary_public_id',
        'secure_url',
        'format',
        'width',
        'height',
        'bytes',
        'is_cover',
        'display_order',
    ];

    protected $hidden = [
        'cloudinary_public_id',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'bytes' => 'integer',
            'is_cover' => 'boolean',
            'display_order' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function url(): ?string
    {
        return $this->image_url ?? $this->secure_url;
    }
}
