@php
    $siteSettings = $siteSettings ?? (\App\Models\SiteSetting::query()->latest('id')->first());
    $associationName = $association->name
        ?? $siteSettings->association_name
        ?? $siteSettings->site_name
        ?? 'الموقع الرسمي للجمعية';

    $rawNews = $news ?? null;
    $newsItem = null;

    if ($rawNews instanceof \Illuminate\Support\Collection) {
        $newsItem = null;
    } elseif ($rawNews instanceof \Illuminate\Database\Eloquent\Collection) {
        $newsItem = null;
    } elseif (is_object($rawNews)) {
        $newsItem = $rawNews;
    } elseif (isset($item) && is_object($item) && !($item instanceof \Illuminate\Support\Collection) && !($item instanceof \Illuminate\Database\Eloquent\Collection)) {
        $newsItem = $item;
    }

    $currentPage = (isset($page) && is_object($page) && !($page instanceof \Illuminate\Support\Collection) && !($page instanceof \Illuminate\Database\Eloquent\Collection))
        ? $page
        : null;

    $currentProject = (isset($project) && is_object($project) && !($project instanceof \Illuminate\Support\Collection) && !($project instanceof \Illuminate\Database\Eloquent\Collection))
        ? $project
        : null;

    $resolvedTitle = trim(
        $title
        ?? $metaTitle
        ?? ($currentPage->meta_title ?? null)
        ?? ($newsItem->meta_title ?? null)
        ?? ($currentProject->meta_title ?? null)
        ?? ($currentPage->title ?? null)
        ?? ($newsItem->title ?? null)
        ?? ($currentProject->title ?? null)
        ?? $associationName
    );

    if ($resolvedTitle !== $associationName && !str_contains($resolvedTitle, $associationName)) {
        $resolvedTitle .= ' | ' . $associationName;
    }

    $resolvedDescriptionSource =
        $description
        ?? $metaDescription
        ?? ($currentPage->meta_description ?? null)
        ?? ($newsItem->meta_description ?? null)
        ?? ($currentProject->meta_description ?? null)
        ?? ($currentPage->excerpt ?? null)
        ?? ($newsItem->excerpt ?? null)
        ?? ($newsItem->summary ?? null)
        ?? ($newsItem->short_description ?? null)
        ?? ($currentProject->description ?? null)
        ?? ($siteSettings->site_description ?? null)
        ?? 'الموقع الرسمي للجمعية';

    $resolvedDescription = \Illuminate\Support\Str::limit(
        trim(strip_tags((string) $resolvedDescriptionSource)),
        160,
        ''
    );

    $canonicalUrl = $canonicalUrl ?? url()->current();

    $rawImage = null;

    if (!empty($currentProject?->coverMedia?->file)) {
        $rawImage = $currentProject->coverMedia->file;
    } elseif (!empty($newsItem?->featuredMedia?->file)) {
        $rawImage = $newsItem->featuredMedia->file;
    } elseif (!empty($newsItem?->image)) {
        $rawImage = $newsItem->image;
    } elseif (!empty($siteSettings?->logoMedia?->file)) {
        $rawImage = $siteSettings->logoMedia->file;
    }

    $shareImage = null;

    if ($rawImage) {
        if (\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
            $shareImage = $rawImage;
        } elseif (\Illuminate\Support\Str::startsWith($rawImage, 'storage/')) {
            $shareImage = asset($rawImage);
        } else {
            $shareImage = asset('storage/' . ltrim($rawImage, '/'));
        }
    }

    $schemaType = 'WebPage';

    if ($newsItem) {
        $schemaType = 'Article';
    } elseif ($currentProject) {
        $schemaType = 'Article';
    }

    
    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $associationName,
        'url' => url('/'),
    ];

    if (!empty($shareImage)) {
        $organizationSchema['logo'] = [
            '@type' => 'ImageObject',
            'url' => $shareImage,
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => $schemaType,
        'name' => $resolvedTitle,
        'headline' => $resolvedTitle,
        'description' => $resolvedDescription,
        'url' => $canonicalUrl,
    ];

    if ($shareImage) {
        $schema['image'] = [$shareImage];
    }

    $schema['publisher'] = [
        '@type' => 'Organization',
        'name' => $associationName,
    ];

    if ($shareImage) {
        $schema['publisher']['logo'] = [
            '@type' => 'ImageObject',
            'url' => $shareImage,
        ];
    }
@endphp

<title>{{ $resolvedTitle }}</title>
<meta name="description" content="{{ $resolvedDescription }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:locale" content="ar_AR">
<meta property="og:type" content="{{ $newsItem ? 'article' : 'website' }}">
<meta property="og:title" content="{{ $resolvedTitle }}">
<meta property="og:description" content="{{ $resolvedDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:site_name" content="{{ $associationName }}">
@if($shareImage)
<meta property="og:image" content="{{ $shareImage }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $resolvedTitle }}">
<meta name="twitter:description" content="{{ $resolvedDescription }}">
@if($shareImage)
<meta name="twitter:image" content="{{ $shareImage }}">
@endif

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
