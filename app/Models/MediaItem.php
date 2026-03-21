<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaItem extends Model
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
