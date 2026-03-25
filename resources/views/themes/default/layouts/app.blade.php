@include('themes.default.partials.header')

<main class="content-wrapper">
    @yield('hero')
@include('themes.default.partials.breadcrumb')
    @yield('content')
</main>

@include('themes.default.partials.footer')
