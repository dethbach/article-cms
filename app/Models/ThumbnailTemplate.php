<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ThumbnailTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'image_path',
        'text_color',
        'text_position',
        'overlay_color',
        'overlay_opacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'overlay_opacity' => 'integer',
        ];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    // public function getImageUrlAttribute(): string
    // {
    //     return asset('storage/' . $this->image_path);
    // }

    public function getImageUrlAttribute(): string
    {
        $path = $this->image_path;

        // filename only: add the folder
        if (!Str::contains($path, '/')) {
            $path = 'thumbnail-templates/' . $path;
        }

        return asset('storage/' . $path);
    }
}
