<?php

namespace App\Support\Media;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUrl
{
    public static function publicBaseUrl(?string $disk = null): ?string
    {
        $disk = $disk ?: 'public';

        if ($disk === 'public') {
            try {
                return rtrim(Storage::disk('public')->url(''), '/');
            } catch (\Throwable $e) {
                return rtrim(url('/storage'), '/');
            }
        }

        $config = config("filesystems.disks.{$disk}", []);

        foreach ([
            $config['url'] ?? null,
            env('R2_PUBLIC_URL'),
            env('CLOUDFLARE_R2_PUBLIC_URL'),
            env('AWS_URL'),
        ] as $candidate) {
            if (filled($candidate)) {
                return rtrim((string) $candidate, '/');
            }
        }

        return null;
    }

    public static function forDiskPath(?string $disk, $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $path = (string) $path;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $disk = $disk ?: 'public';
        $baseUrl = static::publicBaseUrl($disk);

        if (filled($baseUrl)) {
            return $baseUrl . '/' . ltrim($path, '/');
        }

        try {
            return Storage::disk($disk)->url($path);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
