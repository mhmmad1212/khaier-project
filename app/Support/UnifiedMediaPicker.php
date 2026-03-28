<?php

namespace App\Support;

use App\Models\MediaItem;
use Illuminate\Http\Request;

class UnifiedMediaPicker
{
    public static function selectedPayload(Request $request): array
    {
        $payload = [
            'selected_media_id' => $request->input('selected_media_id'),
            'selected_media_file' => $request->input('selected_media_file'),
            'selected_media_field' => $request->input('selected_media_field'),
            'clear_media' => $request->boolean('clear_media'),
            'clear_media_field' => $request->input('clear_media_field'),
        ];

        $referer = $request->headers->get('referer');

        if ($referer) {
            $query = parse_url($referer, PHP_URL_QUERY);

            if ($query) {
                parse_str($query, $params);

                $payload['selected_media_id'] = $payload['selected_media_id'] ?: ($params['selected_media_id'] ?? null);
                $payload['selected_media_file'] = $payload['selected_media_file'] ?: ($params['selected_media_file'] ?? null);
                $payload['selected_media_field'] = $payload['selected_media_field'] ?: ($params['selected_media_field'] ?? null);
                $payload['clear_media_field'] = $payload['clear_media_field'] ?: ($params['clear_media_field'] ?? null);

                if (! $payload['clear_media'] && isset($params['clear_media'])) {
                    $payload['clear_media'] = (bool) $params['clear_media'];
                }
            }
        }

        return $payload;
    }

    public static function selectedId(Request $request, string $statePath, mixed $currentState = null): mixed
    {
        $payload = static::selectedPayload($request);

        if ($payload['clear_media'] && $payload['clear_media_field'] === $statePath) {
            return null;
        }

        if (
            ! empty($payload['selected_media_id']) &&
            ! empty($payload['selected_media_field']) &&
            $payload['selected_media_field'] === $statePath
        ) {
            return $payload['selected_media_id'];
        }

        return $currentState;
    }

    public static function selectedMedia(Request $request, string $statePath, mixed $currentState = null): ?MediaItem
    {
        $selectedId = static::selectedId($request, $statePath, $currentState);

        if (! $selectedId) {
            return null;
        }

        return MediaItem::query()->find($selectedId);
    }

    public static function buildReturnUrl(Request $request): string
    {
        $currentUrl = url()->full();
        $previousUrl = url()->previous();
        $referer = $request->headers->get('referer');

        $returnUrl = $currentUrl;

        if (str_contains($returnUrl, '/livewire/update')) {
            if ($referer && ! str_contains($referer, '/livewire/update')) {
                $returnUrl = $referer;
            } elseif ($previousUrl && ! str_contains($previousUrl, '/livewire/update')) {
                $returnUrl = $previousUrl;
            }
        }

        $returnUrl = preg_replace('/([&?])(selected_media_id|selected_media_file|selected_media_field|clear_media|clear_media_field)=[^&]*/', '$1', $returnUrl);
        $returnUrl = rtrim(str_replace(['?&', '&&'], ['?', '&'], $returnUrl), '?&');

        return $returnUrl;
    }

    public static function buildPickerUrl(Request $request, string $statePath): string
    {
        return url('/admin/media-picker') . '?' . http_build_query([
            'field' => $statePath,
            'return' => static::buildReturnUrl($request),
        ]);
    }

    public static function buildClearUrl(Request $request, string $statePath): string
    {
        $returnUrl = static::buildReturnUrl($request);

        return $returnUrl . (str_contains($returnUrl, '?') ? '&' : '?') . http_build_query([
            'clear_media' => 1,
            'clear_media_field' => $statePath,
        ]);
    }

    public static function storageKey(Request $request, string $statePath): string
    {
        return 'form_state_' . md5(static::buildReturnUrl($request) . '|' . $statePath);
    }
}
