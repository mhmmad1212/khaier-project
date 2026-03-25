@php
    $segments = request()->segments();

    $map = [
        'page' => 'الصفحات',
        'news' => 'الأخبار',
        'projects' => 'المشاريع',
    ];

    $items = [];

    foreach ($segments as $index => $segment) {
        $url = '/' . implode('/', array_slice($segments, 0, $index + 1));
        $label = urldecode($segment);

        // تحويل كلمات النظام
        if (isset($map[$segment])) {
            $label = $map[$segment];
        }

        // صفحة
        if ($segment === ($page->slug ?? null)) {
            $label = $page->title ?? $label;
        }

        // خبر
        if ($segment === ($news->slug ?? null)) {
            $label = $news->title ?? $label;
        }

        $items[] = [
            'url' => $url,
            'label' => $label,
        ];
    }
@endphp

@if(count($items))
<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-white p-3 rounded-3 shadow-sm">

            <li class="breadcrumb-item">
                <a href="/">الرئيسية</a>
            </li>

            @foreach($items as $item)
                @if($loop->last)
                    <li class="breadcrumb-item active">
                        {{ $item['label'] }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $item['url'] }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endif
            @endforeach

        </ol>
    </nav>
</div>
@endif
