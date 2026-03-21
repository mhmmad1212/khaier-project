<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slider extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'title',
        'description',
        'button_text',
        'button_url',
        'image',
        'image_media_id',
        'sort_order',
        'is_active',
    ];

    public function imageMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'image_media_id');
    }
}
