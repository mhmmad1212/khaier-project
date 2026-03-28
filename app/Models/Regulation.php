<?php

namespace App\Models;

use App\Models\Concerns\HasShortCode;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\SyncsMediaFields;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Regulation extends TenantModel
{
    use HasShortCode;
    use HasUniqueSlug;
    use SyncsMediaFields;

    protected $connection = 'tenant';

    protected array $mediaSyncMap = [
        'file_media_id' => 'file',
    ];

    protected $fillable = [
        'title',
        'year',
        'slug',
        'short_code',
        'description',
        'file',
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
