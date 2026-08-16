<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['parent_id', 'name'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $primaryKey = 'category_id';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'category_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'category_id');
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'category_id', 'category_id');
    }
}
