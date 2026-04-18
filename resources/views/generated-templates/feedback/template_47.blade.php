@extends('themes.default.layouts.app')

@section('content')
@php
    $primaryColor = $siteSettings->primary_color ?? '#127962';
    $buttonColor = $siteSettings->button_color ?? $primaryColor;
    $pageTitle = $page->title ?? 'التغذية الراجعة';
@endphp

<div style="direction: rtl; text-align: right; max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: 'Cairo', 'Tahoma', sans-serif;">

    <style>
        .feedback-page-wrap * {
            box-sizing: border-box;
        }

        .feedback-hero {
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, #0f766e 100%);
            border-radius: 24px;
            padding: 32px 24px;
            color: #fff;
            margin-bottom: 30px;
            box-shadow: 0 12px 30px rgba(18, 121, 98, 0.18);
        }

        .feedback-hero-title {
            margin: 0 0 10px;
            font-size: 32px;
            font-weight: 800;
            line-height: 1.6;
        }

        .feedback-hero-text {
            margin: 0;
            font-size: 15px;
            line-height: 2;
            color: rgba(255, 255, 255, 0.95);
            max-width: 850px;
        }

        .feedback-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .feedback-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .feedback-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.10);
            border-color: {{ $primaryColor }};
        }

        .feedback-card-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .feedback-card-title {
            margin: 0 0 10px;
            font-size: 21px;
            font-weight: 800;
            color: #111827;
            line-height: 1.8;
        }

        .feedback-card-description {
            color: #6b7280;
            font-size: 14px;
            line-height: 2;
            margin-bottom: 18px;
            flex-grow: 1;
            white-space: pre-line;
        }

        .feedback-card-footer {
            margin-top: auto;
            display: flex;
            justify-content: flex-start;
        }

        .feedback-file-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: {{ $buttonColor }};
            color: #fff !important;
            text-decoration: none;
            padding: 11px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            transition: opacity .2s ease, transform .2s ease;
            border: none;
        }

        .feedback-file-btn:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .feedback-empty {
            background: #fff;
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            padding: 40px 20px;
            text-align: center;
            color: #64748b;
            font-size: 16px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .feedback-grid {
                grid-template-columns: 1fr;
            }

            .feedback-hero {
                padding: 24px 18px;
                border-radius: 18px;
            }

            .feedback-hero-title {
                font-size: 25px;
            }

            .feedback-hero-text {
                font-size: 14px;
            }

            .feedback-card {
                padding: 18px;
                border-radius: 16px;
            }

            .feedback-card-title {
                font-size: 18px;
            }
        }
    </style>

    <div class="feedback-page-wrap">
        <div class="feedback-hero">
            <h1 class="feedback-hero-title">{{ $pageTitle }}</h1>
            <p class="feedback-hero-text">
                {{ $page->excerpt ?? 'في هذه الصفحة يمكن استعراض عناصر التغذية الراجعة والمرفقات المرتبطة بها بشكل منظم وواضح.' }}
            </p>
        </div>

        @if(isset($items) && $items->count())
            <div class="feedback-grid">
                @foreach($items as $item)
                    @php
                        $fileUrl = null;

                        if (!empty($item->fileMedia)) {
                            if (!empty($item->fileMedia->url)) {
                                $fileUrl = $item->fileMedia->url;
                            } elseif (!empty($item->fileMedia->file)) {
                                $fileUrl = \App\Support\Media\MediaUrl::forDiskPath($item->fileMedia->disk ?? 'public', $item->fileMedia->file);
                            }
                        } elseif (!empty($item->file)) {
                            $fileUrl = str_starts_with($item->file, 'http')
                                ? $item->file
                                : asset('storage/' . ltrim($item->file, '/'));
                        }
                    @endphp

                    <div class="feedback-card">
                        <div class="feedback-card-badge">
                            <span>التغذية الراجعة</span>
                        </div>

                        <h2 class="feedback-card-title">
                            {{ $item->title }}
                        </h2>

                        @if(!empty($item->description))
                            <div class="feedback-card-description">
                                {{ $item->description }}
                            </div>
                        @else
                            <div class="feedback-card-description">
                                لا يوجد وصف مضاف لهذا العنصر.
                            </div>
                        @endif

                        @if($fileUrl)
                            <div class="feedback-card-footer">
                                <a href="{{ $fileUrl }}" target="_blank" class="feedback-file-btn">
                                    <span>عرض المرفق</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="feedback-empty">
                لا توجد عناصر تغذية راجعة مضافة حالياً.
            </div>
        @endif
    </div>
</div>
@endsection