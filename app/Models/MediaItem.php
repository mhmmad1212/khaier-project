<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;

class MediaItem extends TenantModel
{
    protected $connection = 'tenant';

    protected $fillable = [
        'title',
        'file',
        'hash',
        'disk',
        'directory',
        'mime_type',
        'extension',
        'size',
        'alt_text',
        'is_image',
        'is_active',
    ];
}
