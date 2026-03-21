<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasUniqueSlug;

    protected $connection = 'tenant';

    protected $fillable = [
        'title',
        'slug',
        'featured_media_id',
        'content',
        'status',
        'is_active',
    ];

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'featured_media_id');
    }
}
