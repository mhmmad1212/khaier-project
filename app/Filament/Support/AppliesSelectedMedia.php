<?php

namespace App\Filament\Support;

use App\Models\MediaItem;
use App\Support\UnifiedMediaPicker;

trait AppliesSelectedMedia
{
    protected function applySelectedMedia(array $data, array $map): array
    {
        $payload = UnifiedMediaPicker::selectedPayload(request());

        foreach ($map as $mediaIdField => $pathField) {
            $expectedField = 'data.' . $mediaIdField;

            if (
                $payload['clear_media'] &&
                ! empty($payload['clear_media_field']) &&
                $payload['clear_media_field'] === $expectedField
            ) {
                $data[$mediaIdField] = null;
                $data[$pathField] = null;
                continue;
            }

            if (
                ! empty($payload['selected_media_id']) &&
                ! empty($payload['selected_media_field']) &&
                $payload['selected_media_field'] === $expectedField
            ) {
                $data[$mediaIdField] = $payload['selected_media_id'];
                $data[$pathField] = $payload['selected_media_file'] ?: $data[$pathField] ?? null;
            }

            if (empty($data[$pathField]) && ! empty($data[$mediaIdField])) {
                $media = MediaItem::query()->find($data[$mediaIdField]);

                if ($media && ! empty($media->file)) {
                    $data[$pathField] = $media->file;
                }
            }
        }

        return $data;
    }
}
