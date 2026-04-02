@php
    use App\Models\PageTemplate;
    use Illuminate\Support\Facades\DB;

    $siteSettings = $siteSettings ?? DB::connection('tenant')->table('site_settings')->orderByDesc('id')->first();

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
