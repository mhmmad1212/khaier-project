<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Models\MediaItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaPickerPageController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Auth::guard('tenant')->check(), 403);

        $this->bootTenantFromRequest($request);

        $q = trim((string) $request->get('q', ''));

        $items = MediaItem::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('extension', 'like', "%{$q}%")
                        ->orWhere('id', $q);
                });
            })
            ->where('is_active', true)
            ->orderByDesc('id')
            ->get();

        return view('admin.media-picker-page', [
            'items' => $items,
            'field' => $request->string('field')->toString(),
            'returnUrl' => $request->string('return')->toString(),
            'activeTab' => $request->string('tab')->toString() ?: 'library',
            'search' => $q,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::guard('tenant')->check(), 403);

        $this->bootTenantFromRequest($request);

        $validated = $request->validate([
            'field' => ['nullable', 'string', 'max:255'],
            'return' => ['nullable', 'string', 'max:2000'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf,svg', 'max:20480'],
        ]);

        $file = $request->file('file');
        $disk = $this->resolveMediaDisk();

        $directory = 'media-library/' . now()->format('Y/m');
        $storedPath = $file->store($directory, $disk);

        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        $isImage = Str::startsWith((string) $mimeType, 'image/')
            || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'], true);
        $hash = hash_file('sha256', $file->getRealPath());

        MediaItem::query()->create([
            'title' => $validated['title'] ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file' => $storedPath,
            'hash' => $hash,
            'disk' => $disk,
            'directory' => dirname($storedPath) === '.' ? null : dirname($storedPath),
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $file->getSize(),
            'alt_text' => $validated['alt_text'] ?: null,
            'is_image' => $isImage,
            'is_active' => true,
        ]);

        return redirect()->to(
            url('/admin/media-picker') . '?' . http_build_query([
                'field' => $validated['field'] ?? '',
                'return' => $validated['return'] ?? '',
                'tab' => 'library',
                'uploaded' => 1,
            ])
        );
    }

    protected function resolveMediaDisk(): string
    {
        $preferred = env('MEDIA_UPLOAD_DISK');

        if (filled($preferred) && array_key_exists($preferred, config('filesystems.disks', []))) {
            return $preferred;
        }

        $s3 = config('filesystems.disks.s3', []);

        $hasS3CoreConfig =
            filled($s3['key'] ?? null) &&
            filled($s3['secret'] ?? null) &&
            filled($s3['bucket'] ?? null) &&
            filled($s3['endpoint'] ?? null);

        $hasS3PublicUrl =
            filled($s3['url'] ?? null) ||
            filled(env('R2_PUBLIC_URL')) ||
            filled(env('CLOUDFLARE_R2_PUBLIC_URL')) ||
            filled(env('AWS_URL'));

        if ($hasS3CoreConfig && $hasS3PublicUrl) {
            return 's3';
        }

        return 'public';
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

        abort_unless($association, 404, 'Association not found for media picker.');

        App::instance('currentAssociation', $association);

        Config::set('database.connections.tenant.host', $association->database_host);
        Config::set('database.connections.tenant.port', $association->database_port);
        Config::set('database.connections.tenant.database', $association->database_name);
        Config::set('database.connections.tenant.username', $association->database_username);
        Config::set('database.connections.tenant.password', $association->database_password);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }
}
