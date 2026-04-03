@php
    use App\Models\PageTemplate;
    use Illuminate\Support\Facades\DB;

    $siteSettings = $siteSettings ?? DB::connection('tenant')->table('site_settings')->orderByDesc('id')->first();

    $associationName = $association->name
        ?? $siteSettings->association_name
        ?? $siteSettings->site_name
        ?? 'الموقع الرسمي للجمعية';

    $pageBrowserTitle = $associationName;

    if (isset($page) && is_object($page)) {
        $pageBrowserTitle = trim($page->meta_title ?: $page->title ?: $associationName);
    } elseif (isset($news) && is_object($news)) {
        $pageBrowserTitle = trim($news->meta_title ?: $news->title ?: $associationName);
    } elseif (isset($project) && is_object($project)) {
        $pageBrowserTitle = trim($project->meta_title ?: $project->title ?: $associationName);
    }

    if (!str_contains($pageBrowserTitle, $associationName)) {
        $pageBrowserTitle .= ' | ' . $associationName;
    }

    $innerHeaderView = null;
    $innerFooterView = null;

    if (!empty($siteSettings?->inner_pages_header_template_key)) {
        $innerHeaderTemplate = PageTemplate::query()
            ->where('template_key', $siteSettings->inner_pages_header_template_key)
            ->where('is_active', true)
            ->first();

        if ($innerHeaderTemplate && !empty($innerHeaderTemplate->view_path) && view()->exists($innerHeaderTemplate->view_path)) {
            $innerHeaderView = $innerHeaderTemplate->view_path;
        }
    }

    if (!empty($siteSettings?->inner_pages_footer_template_key)) {
        $innerFooterTemplate = PageTemplate::query()
            ->where('template_key', $siteSettings->inner_pages_footer_template_key)
            ->where('is_active', true)
            ->first();

        if ($innerFooterTemplate && !empty($innerFooterTemplate->view_path) && view()->exists($innerFooterTemplate->view_path)) {
            $innerFooterView = $innerFooterTemplate->view_path;
        }
    }
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageBrowserTitle }}</title>
</head>
<body>

@if($innerHeaderView)
    @include($innerHeaderView)
@else
    @include('themes.default.partials.header')
@endif

<main class="content-wrapper">
    @yield('hero')
    @yield('content')
</main>

@if($innerFooterView)
    @include($innerFooterView)
@else
    @include('themes.default.partials.footer')
@endif

</body>
</html>
