<?php

use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\FrontendPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebsiteController::class, 'home'])->name('website.home');

Route::get('/news', [WebsiteController::class, 'newsIndex'])->name('news.index');

Route::get('/page/{slug}/news/{newsSlug}', [FrontendPageController::class, 'pageNewsShow'])->name('page.news.show');
Route::get('/news/{slug}', [FrontendPageController::class, 'newsShow'])->name('news.show');

Route::get('/page/{slug}/project/{id}', [FrontendPageController::class, 'pageProgramProjectShow'])->name('page.project.show');
Route::get('/page/{slug}', [FrontendPageController::class, 'pageShow'])->name('page.show');
Route::get('/projects/{id}', [FrontendPageController::class, 'programProjectShow'])->name('program-projects.show');

Route::get('/board-members', [WebsiteController::class, 'boardMembers'])->name('board-members.index');


Route::get('/general-assembly', [\App\Http\Controllers\WebsiteController::class, 'generalAssemblyMembers'])
    ->name('general-assembly.index');



Route::get('/employees', [\App\Http\Controllers\WebsiteController::class, 'employeesPage'])
->name('employees.index');


Route::get('/committees', [\App\Http\Controllers\WebsiteController::class, 'committeesPage'])
    ->name('committees.index');

use Illuminate\Support\Facades\Storage;
use App\Models\News;
use Intervention\Image\ImageManager;

Route::post('/admin/delete-news-image/{id}', function ($id) {

    $news = News::findOrFail($id);

    if ($news->image) {
        Storage::disk('public')->delete($news->image);
        $news->image = null;
        $news->save();
    }

    return back();

});


Route::middleware(['web'])->get('/admin/media-library/json', function () {
    return response()->json(
        \App\Models\MediaItem::query()
            ->where('is_active', true)
            ->where('is_image', true)
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title ?: basename((string) $item->file),
                    'file' => $item->file,
                    'url' => asset('storage/' . $item->file),
                    'alt_text' => $item->alt_text,
                ];
            })
            ->values()
    );
});


Route::middleware(['web'])->post('/admin/media-library/upload', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf', 'max:20480'],
        'title' => ['nullable', 'string', 'max:255'],
        'alt_text' => ['nullable', 'string', 'max:255'],
    ]);

    $uploadedFile = $request->file('file');
    $originalRealPath = $uploadedFile->getRealPath();
    $originalClientName = $uploadedFile->getClientOriginalName();
    $originalMimeType = $uploadedFile->getMimeType();
    $originalHash = hash_file('sha256', $originalRealPath);
    $isImage = str_starts_with((string) $originalMimeType, 'image/');

    $extension = strtolower($uploadedFile->getClientOriginalExtension());
    $baseName = now()->format('Ymd_His') . '_' . uniqid();

    if ($isImage) {
        $extension = 'jpg';
    }

    $relativePath = 'tenants/' . config('database.connections.tenant.database') . '/media/' . $baseName . '.' . $extension;
    $absolutePath = storage_path('app/public/' . $relativePath);

    if (! is_dir(dirname($absolutePath))) {
        mkdir(dirname($absolutePath), 0775, true);
    }

    if ($isImage) {
        $manager = new \Intervention\Image\ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver()
        );

        $image = $manager->read($originalRealPath);

        if ($image->width() > 1920) {
            $image->scaleDown(width: 1920);
        }

        $encoded = $image->toJpeg(85);
        file_put_contents($absolutePath, (string) $encoded);
        $mimeType = 'image/jpeg';
        $size = filesize($absolutePath);
    } else {
        $uploadedFile->move(dirname($absolutePath), basename($absolutePath));
        $mimeType = $originalMimeType;
        $size = filesize($absolutePath);
    }

    $media = \App\Models\MediaItem::query()->create([
        'title' => $request->input('title') ?: pathinfo($originalClientName, PATHINFO_FILENAME),
        'file' => $relativePath,
        'hash' => $originalHash,
        'disk' => 'public',
        'directory' => dirname($relativePath),
        'mime_type' => $mimeType,
        'extension' => $extension,
        'size' => $size,
        'alt_text' => $request->input('alt_text'),
        'is_image' => $isImage,
        'is_active' => true,
    ]);

    $duplicate = \App\Models\MediaItem::query()
        ->where('hash', $originalHash)
        ->where('id', '!=', $media->id)
        ->exists();

    return response()->json([
        'success' => true,
        'item' => [
            'id' => $media->id,
            'title' => $media->title ?: basename((string) $media->file),
            'file' => $media->file,
            'url' => asset('storage/' . $media->file),
            'alt_text' => $media->alt_text,
            'is_image' => (bool) $media->is_image,
            'extension' => $media->extension,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
        ],
        'duplicate' => $duplicate,
        'message' => $duplicate
            ? 'تم رفع الملف، ويوجد ملف مشابه مرفوع سابقًا.'
            : 'تم رفع الملف بنجاح.',
    ]);
});


Route::middleware(['web'])->post('/admin/media-library/update/{id}', function (\Illuminate\Http\Request $request, $id) {
    $media = \App\Models\MediaItem::query()->findOrFail($id);

    $request->validate([
        'title' => ['nullable', 'string', 'max:255'],
        'alt_text' => ['nullable', 'string', 'max:255'],
    ]);

    $media->title = $request->input('title');
    $media->alt_text = $request->input('alt_text');
    $media->save();

    return response()->json([
        'success' => true,
    ]);
});

Route::middleware(['web'])->delete('/admin/media-library/delete/{id}', function ($id) {
    $media = \App\Models\MediaItem::query()->findOrFail($id);

    if ($media->file && \Illuminate\Support\Facades\Storage::disk('public')->exists($media->file)) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file);
    }

    $media->delete();

    return response()->json([
        'success' => true,
    ]);
});


Route::middleware(['web'])->get('/admin/media-library/picker-json', function () {
    return response()->json(
        \App\Models\MediaItem::query()
            ->where('is_active', true)
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title ?: basename((string) $item->file),
                    'file' => $item->file,
                    'url' => asset('storage/' . $item->file),
                    'alt_text' => $item->alt_text,
                    'is_image' => (bool) $item->is_image,
                    'extension' => $item->extension,
                    'mime_type' => $item->mime_type,
                    'size' => $item->size,
                ];
            })
            ->values()
    );
});


Route::middleware(['web'])->get('/admin/media-library/usage/{id}', function ($id) {

    $usage = [];

    if (class_exists(\App\Models\News::class)) {
        $news = \App\Models\News::where('featured_media_id', $id)->get(['id','title']);
        foreach ($news as $item) {
            $usage[] = [
                'type' => 'خبر',
                'title' => $item->title,
                'url' => '/admin/news/'.$item->id.'/edit'
            ];
        }
    }

    if (class_exists(\App\Models\Page::class)) {
        $pages = \App\Models\Page::where('featured_media_id', $id)->get(['id','title']);
        foreach ($pages as $item) {
            $usage[] = [
                'type' => 'صفحة',
                'title' => $item->title,
                'url' => '/admin/pages/'.$item->id.'/edit'
            ];
        }
    }

    if (class_exists(\App\Models\BoardMember::class)) {
        $members = \App\Models\BoardMember::where('photo_media_id', $id)->get(['id','name']);
        foreach ($members as $item) {
            $usage[] = [
                'type' => 'عضو مجلس',
                'title' => $item->name,
                'url' => '/admin/board-members/'.$item->id.'/edit'
            ];
        }
    }

    if (class_exists(\App\Models\Employee::class)) {
        $emp = \App\Models\Employee::where('photo_media_id', $id)->get(['id','name']);
        foreach ($emp as $item) {
            $usage[] = [
                'type' => 'موظف',
                'title' => $item->name,
                'url' => '/admin/employees/'.$item->id.'/edit'
            ];
        }
    }

    return response()->json($usage);

});



Route::get('/policies', function () {
    $items = \App\Models\Policy::query()
        ->where('is_active', true)
        ->with('fileMedia')
        ->orderByDesc('published_at')
        ->orderBy('sort_order')
        ->get();

    $siteSettings = \Illuminate\Support\Facades\DB::connection('tenant')
        ->table('site_settings')
        ->orderByDesc('id')
        ->first();

    $template = null;

    if ($siteSettings && !empty($siteSettings->policies_template_key)) {
        $template = \App\Models\PageTemplate::query()
            ->where('template_key', $siteSettings->policies_template_key)
            ->where('is_active', true)
            ->first();
    }

    $viewPath = ($template && !empty($template->view_path))
        ? $template->view_path
        : 'themes.default.policies.index';

    return view($viewPath, compact('items', 'template', 'siteSettings'));
});

Route::get('/regulations', function () {
    $items = \App\Models\Regulation::query()
        ->where('is_active', true)
        ->with('fileMedia')
        ->orderByDesc('published_at')
        ->orderBy('sort_order')
        ->get();

    return view('themes.default.regulations.index', compact('items'));
});

Route::get('/financial-reports', function () {
    $items = \App\Models\FinancialReport::query()
        ->where('is_active', true)
        ->with('fileMedia')
        ->orderByDesc('year')
        ->orderByDesc('published_at')
        ->orderBy('sort_order')
        ->get();

    return view('themes.default.financial-reports.index', compact('items'));
});




Route::get('/s/{code}', function ($code) {
    $maps = [
        [\App\Models\News::class, '/news/'],
        [\App\Models\Policy::class, '/policies/'],
        [\App\Models\Regulation::class, '/regulations/'],
        [\App\Models\FinancialReport::class, '/financial-reports/'],
    ];

    foreach ($maps as [$model, $prefix]) {
        $item = $model::query()->where('short_code', $code)->first();
        if ($item) {
            return redirect($prefix . ($item->slug ?: $item->id));
        }
    }

    abort(404);
});

Route::get('/s/{code}', [\App\Http\Controllers\ShortLinkController::class, 'resolve']);
