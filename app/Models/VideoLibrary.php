<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;

class VideoLibrary extends TenantModel
{
    protected $fillable = [
        'title',
        'youtube_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $url = $this->youtube_url;

        if (! $url) {
            return null;
        }

        if (preg_match('/youtube\.com\/watch\?v=([^\&]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/youtu\.be\/([^\?&]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/youtube\.com\/embed\/([^\?&]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return $url;
    }
}
