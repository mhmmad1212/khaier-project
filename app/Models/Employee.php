<?php

namespace App\Models;

use App\Models\Concerns\SyncsMediaFields;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends TenantModel
{
    use SyncsMediaFields;

    protected $connection = 'tenant';

    protected array $mediaSyncMap = [
        'photo_media_id' => 'photo',
    ];

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

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function photoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'photo_media_id');
    }
}
