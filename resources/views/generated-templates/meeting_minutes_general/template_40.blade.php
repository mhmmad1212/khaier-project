@extends('themes.default.layouts.app')

@section('content')
@php
    $meetingMinutes = collect($meetingMinutes ?? ($items ?? []));
    $buttonColor = $siteSettings->button_color ?? $siteSettings->primary_color ?? '#127962';
    $secondaryColor = $siteSettings->secondary_color ?? '#10b981';

    $categoryLabel = $categoryLabel ?? ($page->title ?? 'محاضر الاجتماعات');

    $meetingTypeLabels = [
        'regular' => 'عادي / دوري',
        'emergency' => 'طارئ',
    ];

    $meetingTypeColors = [
        'regular' => '#64748b',
        'emergency' => '#dc2626',
    ];
@endphp

<div style="max-width:1200px; margin:40px auto; padding:0 16px; direction:rtl; text-align:right; font-family:'Cairo', Tahoma, Arial, sans-serif;">
    <div style="background:linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%); border-radius:28px; padding:40px 26px; color:#fff; margin-bottom:28px; box-shadow:0 20px 40px rgba(15,23,42,.14);">
        <h1 style="margin:0 0 10px; font-size:34px; font-weight:900;">
            {{ $page->title ?? $categoryLabel }}
        </h1>

        <div style="font-size:16px; line-height:2; opacity:.95; max-width:760px;">
            {{ $page->excerpt ?? ('استعراض جميع المحاضر الخاصة بـ ' . $categoryLabel . ' مع إمكانية فتح المرفقات الخاصة بكل محضر.') }}
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:24px;">
        @forelse($meetingMinutes as $meetingMinute)
            @php
                $fileUrl = $meetingMinute->fileMedia->url
                    ?? (
                        !empty($meetingMinute->file)
                            ? (
                                \Illuminate\Support\Str::startsWith($meetingMinute->file, ['http://', 'https://'])
                                    ? $meetingMinute->file
                                    : \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($meetingMinute->file, '/'))
                            )
                            : null
                    );

                $meetingTypeLabel = $meetingTypeLabels[$meetingMinute->meeting_type] ?? ($meetingMinute->meeting_type ?: 'غير محدد');
                $meetingTypeColor = $meetingTypeColors[$meetingMinute->meeting_type] ?? '#64748b';
            @endphp

            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:24px; padding:24px; box-shadow:0 14px 28px rgba(15,23,42,.06); display:flex; flex-direction:column; gap:16px;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
                    <h3 style="margin:0; font-size:24px; font-weight:900; color:#111827; line-height:1.6;">
                        {{ $meetingMinute->title }}
                    </h3>

                    <span style="display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; background:{{ $meetingTypeColor }}; color:#fff; font-size:13px; font-weight:800; white-space:nowrap;">
                        {{ $meetingTypeLabel }}
                    </span>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:14px;">
                        <div style="font-size:13px; color:#6b7280; font-weight:700; margin-bottom:6px;">تاريخ الاجتماع</div>
                        <div style="font-size:15px; color:#111827; font-weight:800;">
                            {{ optional($meetingMinute->meeting_date)->format('Y-m-d') ?: 'غير محدد' }}
                        </div>
                    </div>

                    <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:14px;">
                        <div style="font-size:13px; color:#6b7280; font-weight:700; margin-bottom:6px;">جهة الاجتماع</div>
                        <div style="font-size:15px; color:#111827; font-weight:800;">
                            {{ $categoryLabel }}
                        </div>
                    </div>
                </div>

                @if(!empty($meetingMinute->description))
                    <div style="color:#4b5563; line-height:2; font-size:15px;">
                        {!! nl2br(e(\Illuminate\Support\Str::limit($meetingMinute->description, 260))) !!}
                    </div>
                @endif

                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:auto;">
                    @if($fileUrl)
                        <a href="{{ $fileUrl }}" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 16px; border-radius:12px; border:1px solid {{ $buttonColor }}; color:#fff; background:{{ $buttonColor }}; text-decoration:none; font-weight:800;">
                            <i class="fas fa-file-arrow-down"></i>
                            عرض المرفق
                        </a>
                    @else
                        <span style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 16px; border-radius:12px; border:1px solid #e5e7eb; color:#64748b; background:#f8fafc; font-weight:800;">
                            <i class="fas fa-ban"></i>
                            لا يوجد مرفق
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; background:#fff; border:1px dashed #cbd5e1; border-radius:24px; padding:54px 20px; text-align:center; color:#6b7280;">
                لا توجد محاضر حالياً.
            </div>
        @endforelse
    </div>
</div>
@endsection