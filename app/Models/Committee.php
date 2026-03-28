<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;

class Committee extends TenantModel
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'description',
        'chairman',
        'members_count',
        'attachment',
        'sort_order',
        'is_active',
        'attachment_media_id',
    ];


    public function attachmentMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'attachment_media_id');
    }

}
