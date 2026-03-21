@php
    $rootItems = $items ?? collect();

    $renderMenu = function ($nodes, $depth = 0) use (&$renderMenu) {
        $ulClass = $depth === 0 ? 'site-nav level-0' : 'site-subnav level-' . $depth;

        echo '<ul class="' . $ulClass . '">';

        foreach ($nodes as $node) {
            $children = $node->children()->orderBy('sort_order')->get();
            $hasChildren = $children->count() > 0;

            echo '<li class="site-nav-item depth-' . $depth . ($hasChildren ? ' has-children' : '') . '">';

            echo '<a href="' . e($hasChildren ? '#' : ($node->resolved_url ?? $node->url ?? '#')) . '"';
            echo ' class="site-nav-link"';
            if ($hasChildren) {
                echo ' data-nav-toggle="true"';
                echo ' onclick="return window.siteNavToggle(event, this);"';
            }
            echo ' target="' . e($node->target ?? '_self') . '"';
            echo '>';

            echo '<span class="site-nav-link-main">';
            if (!empty($node->icon)) {
                echo '<i class="' . e($node->icon) . '"></i>';
            }
            echo '<span>' . e($node->title) . '</span>';
            echo '</span>';

            if ($hasChildren) {
                echo '<span class="site-nav-arrow">' . ($depth === 0 ? '▾' : '‹') . '</span>';
            }

            echo '</a>';

            if ($hasChildren) {
                $renderMenu($children, $depth + 1);
            }

            echo '</li>';
        }

        echo '</ul>';
    };
@endphp

<div class="site-nav-wrapper" id="siteNavWrapper">
    @php $renderMenu($rootItems, 0); @endphp
</div>

<script>
(function () {
    if (window.siteNavToggle) return;

    window.siteNavIsMobile = function () {
        return window.innerWidth <= 991.98;
    };

    window.siteNavToggle = function (event, linkEl) {
        const item = linkEl.closest('.site-nav-item');
        if (!item) return false;

        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const parentList = item.parentElement;
        if (parentList) {
            parentList.querySelectorAll(':scope > .site-nav-item.open').forEach((el) => {
                if (el !== item) el.classList.remove('open');
            });
        }

        item.classList.toggle('open');
        return false;
    };

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#siteNavWrapper')) {
            document.querySelectorAll('#siteNavWrapper .site-nav-item.open').forEach((el) => {
                el.classList.remove('open');
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#siteNavWrapper .site-nav-item.has-children').forEach((item) => {
            item.addEventListener('mouseenter', function () {
                if (!window.siteNavIsMobile()) this.classList.add('open');
            });

            item.addEventListener('mouseleave', function () {
                if (!window.siteNavIsMobile()) this.classList.remove('open');
            });
        });
    });
})();
</script>
