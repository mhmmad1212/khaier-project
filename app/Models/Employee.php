<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'position',
        'department',
        'photo',
        'photo_media_id',
        'bio',
        'email',
        'phone',
        'sort_order',
        'is_active',
    ];

    public function photoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'photo_media_id');
    }
}
