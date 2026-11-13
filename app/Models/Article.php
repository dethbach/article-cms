<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id',
        'title', 'slug', 'excerpt', 'content',
        'thumbnail_type', 'thumbnail_path', 'template_article_id',
        'meta_title', 'meta_description', 'focus_keyword', 'secondary_keywords',
        'canonical_url', 'og_title', 'og_description',
        'allow_indexing', 'allow_following',
        'key_takeaways', 'faq', 'schema_type', 'schema_data', 'source_references',
        'last_reviewed_at',
        'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'secondary_keywords' => 'array',
            'key_takeaways' => 'array',
            'faq' => 'array',
            'schema_data' => 'array',
            'source_references' => 'array',

            'allow_indexing' => 'boolean',
            'allow_following' => 'boolean',
            'is_published' => 'boolean',

            'last_reviewed_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    // Author
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Thumbnail Template
    public function templateArticle(): BelongsTo
    {
        return $this->belongsTo(ThumbnailTemplate::class, 'template_article_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_published) {
            return 'draft';
        }

        return $this->published_at && $this->published_at->isFuture()
            ? 'scheduled'
            : 'published';
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags((string) $this->content));

        return max(1, (int) ceil($words / 200));
    }

    protected static function booted(): void
    {
        $clear = fn () => Cache::forget('seo.sitemap')
            || Cache::forget('seo.llms')
            || Cache::forget('seo.feed');

        static::saved($clear);
        static::deleted($clear);
        static::restored($clear);
    }
}
