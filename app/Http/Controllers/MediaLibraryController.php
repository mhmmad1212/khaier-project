<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\MediaItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function pickerJson(Request $request): JsonResponse
    {
        if (! Auth::guard('tenant')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $this->bootTenantFromRequest($request);

        $items = MediaItem::query()
            ->where('is_active', true)
            ->latest('id')
            ->get()
            ->map(function (MediaItem $item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title ?: basename((string) $item->file),
                    'url' => $this->mediaUrl($item),
                    'is_image' => (bool) $item->is_image,
                    'extension' => $item->extension,
                    'size' => $item->size,
                    'alt_text' => $item->alt_text,
                ];
            })
            ->values();

        return response()->json($items);
    }

    public function usage(Request $request, int $itemId): JsonResponse
    {
        if (! Auth::guard('tenant')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $this->bootTenantFromRequest($request);

        return response()->json([]);
    }

    public function upload(Request $request): JsonResponse
    {
        if (! Auth::guard('tenant')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $this->bootTenantFromRequest($request);

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf', 'max:20480'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('file');

        $disk = 'public';
        $directory = 'media-library/' . now()->format('Y/m');
        $storedPath = $file->store($directory, $disk);

        $hash = hash_file('sha256', $file->getRealPath());

        $existing = MediaItem::query()->where('hash', $hash)->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'duplicate' => true,
                'item' => [
                    'id' => $existing->id,
                    'title' => $existing->title ?: basename((string) $existing->file),
                    'url' => $this->mediaUrl($existing),
                    'is_image' => (bool) $existing->is_image,
                    'extension' => $existing->extension,
                    'size' => $existing->size,
                    'alt_text' => $existing->alt_text,
                ],
            ]);
        }

        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        $isImage = Str::startsWith((string) $mimeType, 'image/');

        $item = MediaItem::query()->create([
            'title' => $request->input('title') ?: $file->getClientOriginalName(),
            'file' => $storedPath,
            'hash' => $hash,
            'disk' => $disk,
            'directory' => dirname($storedPath) === '.' ? null : dirname($storedPath),
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $file->getSize(),
            'alt_text' => $request->input('alt_text') ?: null,
            'is_image' => $isImage,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'duplicate' => false,
            'item' => [
                'id' => $item->id,
                'title' => $item->title ?: basename((string) $item->file),
                'url' => $this->mediaUrl($item),
                'is_image' => (bool) $item->is_image,
                'extension' => $item->extension,
                'size' => $item->size,
                'alt_text' => $item->alt_text,
            ],
        ]);
    }

    protected function bootTenantFromRequest(Request $request): void
    {
        $association = App::bound('currentAssociation')
            ? App::make('currentAssociation')
            : null;

        if (! $association) {
            $host = $request->getHost();
            $association = Association::where('domain', $host)->first();
        }

        abort_unless($association, 404, 'Association not found for media library.');

        App::instance('currentAssociation', $association);

        Config::set('database.connections.tenant.host', $association->database_host);
        Config::set('database.connections.tenant.port', $association->database_port);
        Config::set('database.connections.tenant.database', $association->database_name);
        Config::set('database.connections.tenant.username', $association->database_username);
        Config::set('database.connections.tenant.password', $association->database_password);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    protected function mediaUrl(MediaItem $item): string
    {
        try {
            return Storage::disk($item->disk ?: 'public')->url($item->file);
        } catch (\Throwable $e) {
            return Storage::disk('public')->url($item->file);
        }
    }
}
