<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaItem extends TenantModel
{
    protected $connection = 'tenant';

    protected $fillable = [
        'title',
        'file',
        'hash',
        'disk',
        'directory',
        'mime_type',
        'extension',
        'size',
        'alt_text',
        'is_image',
        'is_active',
    ];

    protected $appends = [
        'url',
    ];

    public function getUrlAttribute(): ?string
    {
        if (! filled($this->file)) {
            return null;
        }

        if (Str::startsWith((string) $this->file, ['http://', 'https://'])) {
            return $this->file;
        }

        $disk = $this->disk ?: 'public';

        if ($disk === 'public') {
            try {
                return Storage::disk('public')->url($this->file);
            } catch (\Throwable $e) {
                return url('/storage/' . ltrim($this->file, '/'));
            }
        }

        $baseUrl = $this->resolvePublicBaseUrl($disk);

        if (filled($baseUrl)) {
            return rtrim($baseUrl, '/') . '/' . ltrim($this->file, '/');
        }

        try {
            return Storage::disk($disk)->url($this->file);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function resolvePublicBaseUrl(string $disk): ?string
    {
        $candidates = [
            config("filesystems.disks.{$disk}.url"),
            env('R2_PUBLIC_URL'),
            env('CLOUDFLARE_R2_PUBLIC_URL'),
            env('AWS_URL'),
        ];

        foreach ($candidates as $candidate) {
            if (filled($candidate)) {
                return (string) $candidate;
            }
        }

        return null;
    }
}
