<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaPickerPageController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Auth::guard('tenant')->check(), 403);

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

        $validated = $request->validate([
            'field' => ['nullable', 'string', 'max:255'],
            'return' => ['nullable', 'string', 'max:2000'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf,svg', 'max:20480'],
        ]);

        $file = $request->file('file');
        $disk = 'public';
        $directory = 'media-library/' . now()->format('Y/m');
        $storedPath = $file->store($directory, $disk);

        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        $isImage = Str::startsWith((string) $mimeType, 'image/');
        $fullPath = storage_path('app/public/' . $storedPath);

        MediaItem::query()->create([
            'title' => $validated['title'] ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file' => $storedPath,
            'hash' => file_exists($fullPath) ? hash_file('sha256', $fullPath) : null,
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
}
