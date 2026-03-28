<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partner extends TenantModel
{
    protected $fillable = [
        'name',
        'logo',
        'logo_media_id',
        'url',
        'sort_order',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::saving(function (Partner $partner) {
            if ($partner->logo_media_id) {
                $media = MediaItem::query()->find($partner->logo_media_id);

                if ($media && ! empty($media->file)) {
                    $partner->logo = $media->file;
                }
            }
        });
    }

    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'logo_media_id');
    }
}
