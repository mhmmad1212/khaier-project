@php
    $children = $item->children()->orderBy('sort_order')->get();
    $hasChildren = $children->count() > 0;
@endphp

<li class="{{ $depth === 0 ? 'nav-item' : '' }} {{ $hasChildren ? 'dropdown dropdown-submenu' : '' }}">
    <a
        class="{{ $depth === 0 ? 'nav-link' : 'dropdown-item' }} {{ $hasChildren ? 'has-submenu' : '' }}"
        href="{{ $hasChildren ? '#' : ($item->resolved_url ?? $item->url ?? '#') }}"
        @if($hasChildren)
            data-menu-toggle="submenu"
            data-depth="{{ $depth }}"
        @endif
        target="{{ $item->target ?? '_self' }}"
    >
        <span class="menu-link-content">
            @if(!empty($item->icon))
                <i class="{{ $item->icon }}"></i>
            @endif
            <span>{{ $item->title }}</span>
        </span>

        @if($hasChildren)
            <span class="menu-arrow">
                {{ $depth === 0 ? '▾' : '‹' }}
            </span>
        @endif
    </a>

    @if($hasChildren)
        <ul class="dropdown-menu submenu-depth-{{ $depth + 1 }}">
            @foreach($children as $child)
                @include('themes.default.partials.menu-item', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </ul>
    @endif
</li>
