@if(!empty($mainMenu) && $mainMenu->items->count())
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        @foreach($mainMenu->items as $item)
            @php
                $hasChildren = $item->children && $item->children->count();
                $target = $item->target ?: '_self';
            @endphp

            @if($hasChildren)
                <li class="nav-item dropdown">
                    <a
                        class="nav-link dropdown-toggle"
                        href="{{ $item->resolved_url }}"
                        id="menu-item-{{ $item->id }}"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        target="{{ $target }}"
                    >
                        @if(!empty($item->icon))
                            <i class="{{ $item->icon }}"></i>
                        @endif
                        <span>{{ $item->title }}</span>
                    </a>

                    <ul class="dropdown-menu" aria-labelledby="menu-item-{{ $item->id }}">
                        @foreach($item->children as $child)
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="{{ $child->resolved_url }}"
                                    target="{{ $child->target ?: '_self' }}"
                                >
                                    @if(!empty($child->icon))
                                        <i class="{{ $child->icon }}"></i>
                                    @endif
                                    <span>{{ $child->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @else
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="{{ $item->resolved_url }}"
                        target="{{ $target }}"
                    >
                        @if(!empty($item->icon))
                            <i class="{{ $item->icon }}"></i>
                        @endif
                        <span>{{ $item->title }}</span>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
@endif
