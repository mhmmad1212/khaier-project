<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends TenantModel
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
