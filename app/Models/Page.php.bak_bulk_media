<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends TenantModel
{
    use HasUniqueSlug;

    protected $fillable = [
        'template_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'page_type',
        'system_key',
        'featured_image',
        'featured_media_id',
        'status',
        'published_at',
        'sort_order',
        'meta_title',
        'meta_description',
        'allow_tenant_edit',
        'is_active',
    ];

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'featured_media_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PageTemplate::class, 'template_id');
    }
}
