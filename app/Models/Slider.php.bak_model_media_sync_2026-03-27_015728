<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slider extends TenantModel
{
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

    protected static function booted(): void
    {
        static::saving(function (Slider $slider) {
            if ($slider->image_media_id) {
                $media = MediaItem::query()->find($slider->image_media_id);

                if ($media && ! empty($media->file)) {
                    $slider->image = $media->file;
                }
            }
        });
    }

    public function imageMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'image_media_id');
    }
}
