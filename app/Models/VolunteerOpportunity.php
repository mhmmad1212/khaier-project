<?php

namespace App\Models;

use App\Models\Concerns\SyncsMediaFields;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VolunteerOpportunity extends TenantModel
{
    use SyncsMediaFields;

    protected $connection = 'tenant';

    protected array $mediaSyncMap = [
        'image_media_id' => 'image',
    ];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'opportunity_type',
        'start_date',
        'end_date',
        'hours_count',
        'platform_url',
        'image',
        'image_media_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'hours_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function typeOptions(): array
    {
        return [
            'social' => 'اجتماعي',
            'relief' => 'إغاثي',
            'medical' => 'طبي',
            'digital' => 'رقمي',
            'other' => 'أخرى',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return static::typeOptions()[$this->opportunity_type] ?? 'أخرى';
    }

    public function imageMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'image_media_id');
    }

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if (blank($model->slug) && filled($model->title)) {
                $base = Str::slug($model->title);
                if (blank($base)) {
                    $base = 'volunteer-opportunity';
                }

                $slug = $base;
                $counter = 2;

                while (
                    static::query()
                        ->where('slug', $slug)
                        ->when($model->exists, fn ($q) => $q->whereKeyNot($model->getKey()))
                        ->exists()
                ) {
                    $slug = $base . '-' . $counter;
                    $counter++;
                }

                $model->slug = $slug;
            }
        });
    }
}
