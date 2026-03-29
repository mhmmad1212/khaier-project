<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;

class Committee extends TenantModel
{
    protected $connection = 'tenant';

    protected $guarded = [];


    public function attachmentMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'attachment_media_id');
    }

}
