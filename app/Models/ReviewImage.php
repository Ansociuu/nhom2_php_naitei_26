<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewImage extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'review_images';

    protected $primaryKey = 'image_id';

    protected $fillable = [
        'review_id',
        'image_url',
        'cloudinary_public_id',
        'secure_url',
        'format',
        'width',
        'height',
        'bytes',
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
            'display_order' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class, 'review_id', 'review_id');
    }

    /**
     * image_url lưu đường dẫn tương đối (ảnh người dùng tải lên) hoặc URL đầy đủ.
     */
    public function url(): ?string
    {
        $path = $this->image_url ?? $this->secure_url;

        if ($path === null || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }
}
