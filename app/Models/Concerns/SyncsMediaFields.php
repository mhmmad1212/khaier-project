<?php

namespace App\Models\Concerns;

use App\Models\MediaItem;

trait SyncsMediaFields
{
    protected static function bootSyncsMediaFields(): void
    {
        static::saving(function ($model) {
            if (! property_exists($model, 'mediaSyncMap') || ! is_array($model->mediaSyncMap)) {
                return;
            }

            foreach ($model->mediaSyncMap as $mediaIdField => $pathField) {
                $mediaId = $model->{$mediaIdField} ?? null;

                if (! empty($mediaId)) {
                    $media = MediaItem::query()->find($mediaId);

                    if ($media && ! empty($media->file)) {
                        $model->{$pathField} = $media->file;
                    }
                }
            }
        });
    }
}
