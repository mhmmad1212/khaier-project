<?php

namespace App\Models;

use App\Models\Concerns\HasShortCode;

use App\Models\Concerns\HasUniqueSlug;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Regulation extends Model
{
    use HasShortCode;

    use HasUniqueSlug;

    protected $connection = 'tenant';

    protected $fillable = [
        'title',
        'year',
        'slug',
        'short_code',
        'description',
        'file_media_id',
        'published_at',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function fileMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'file_media_id');
    }
}
