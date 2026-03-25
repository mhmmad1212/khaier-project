<?php

namespace App\Models;

use App\Models\Concerns\HasShortCode;

use App\Models\Concerns\HasUniqueSlug;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends TenantModel
{
    use HasShortCode;

    use HasUniqueSlug;

    protected $connection = 'tenant';

    protected $fillable = [
        'meta_title',
        'meta_description',
        'title',
        'slug',
        'short_code',
        'excerpt',
        'content',
        'image',
        'image_media_id',
        'published_at',
        'status',
        'is_active',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NewsCategory::class, 'news_category_news');
    }

    public function imageMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'image_media_id');
    }
}
