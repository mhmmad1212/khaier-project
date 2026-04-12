<?php

namespace App\Models;

use App\Models\Concerns\SyncsMediaFields;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExecutiveDirectorProfile extends TenantModel
{
    use SyncsMediaFields;

    protected $connection = 'tenant';

    protected array $mediaSyncMap = [
        'image_media_id' => 'image',
    ];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'bio',
        'image',
        'image_media_id',
    ];

    public function imageMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'image_media_id');
    }
}
