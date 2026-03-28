<?php

namespace App\Models;

use App\Models\Concerns\SyncsMediaFields;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Policy extends TenantModel
{
    use SyncsMediaFields;

    protected array $mediaSyncMap = [
        'file_media_id' => 'file',
    ];

    protected $fillable = [
        'title',
        'description',
        'file',
        'file_media_id',
        'sort_order',
        'is_active',
    ];

    public function fileMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'file_media_id');
    }
}
