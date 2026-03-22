@extends('themes.default.layouts.app')

@section('content')
@php
    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#2ea36b';

    $membersList = collect($items ?? ($members ?? $generalAssemblyMembers ?? []));
@endphp

<div style="direction: rtl; text-align: right; max-width: 1200px; margin: 40px auto; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-sizing: border-box;">

    <style>
        .ga-header {
            border-bottom: 2px solid {{ $buttonColor }};
            padding-bottom: 15px;
            margin-bottom: 35px;
        }

        .ga-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
        }

        .ga-card {
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            padding: 26px 20px 22px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            text-align: center;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            position: relative;
            overflow: hidden;
        }

        .ga-card::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            height: 5px;
            background: {{ $buttonColor }};
            opacity: .9;
        }

        .ga-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.10);
            border-color: rgba(0, 0, 0, 0.08);
        }

        .ga-avatar-wrap {
            width: 124px;
            height: 124px;
            margin: 8px auto 18px;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, {{ $buttonColor }}, rgba(255,255,255,0.7));
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
        }

        .ga-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            background: #f3f4f6;
        }

        .ga-avatar-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 700;
        }

        .ga-name {
            font-size: 20px;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 10px;
            line-height: 1.7;
        }

        .ga-role {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 999px;
            background: rgba(46, 163, 107, 0.10);
            color: {{ $buttonColor }};
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .ga-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 8px;
        }

        .ga-meta-item {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.8;
        }

        .ga-meta-label {
            font-weight: 700;
            color: #111827;
        }

        .ga-empty {
            text-align: center;
            padding: 70px 20px;
            background: #f9fafb;
            border: 2px dashed #cbd5e0;
            border-radius: 16px;
            color: #6b7280;
            font-size: 18px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .ga-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="ga-header">
        <h1 style="color: #1a4a38; font-size: 30px; font-weight: 800; margin: 0 0 8px;">أعضاء الجمعية العمومية</h1>
        <p style="margin: 0; color: #6b7280; font-size: 15px;">
            تعرف على أعضاء الجمعية العمومية ودورهم في دعم مسيرة الجمعية.
        </p>
    </div>

    @if($membersList->count())
        <div class="ga-grid">
            @foreach($membersList as $item)
                @php
                    $photo = $item->photo
                        ?? ($item->photoMedia->file ?? null)
                        ?? ($item->image ?? null);

                    $name = $item->name ?? $item->title ?? 'بدون اسم';
                    $position = $item->position ?? $item->role ?? 'عضو الجمعية العمومية';
                    $joinDate = $item->join_date ?? $item->created_at ?? null;
                @endphp

                <div class="ga-card">
                    <div class="ga-avatar-wrap">
                        @if(!empty($photo))
                            <img
                                src="{{ asset('storage/' . ltrim($photo, '/')) }}"
                                alt="{{ $name }}"
                                class="ga-avatar"
                            >
                        @else
                            <div class="ga-avatar-placeholder">
                                لا توجد صورة
                            </div>
                        @endif
                    </div>

                    <h2 class="ga-name">{{ $name }}</h2>

                    @if(!empty($position))
                        <div class="ga-role">{{ $position }}</div>
                    @endif

                    <div class="ga-meta">
                        @if(!empty($joinDate))
                            <div class="ga-meta-item">
                                <span class="ga-meta-label">تاريخ الانضمام:</span>
                                {{ \Illuminate\Support\Carbon::parse($joinDate)->format('Y-m-d') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="ga-empty">
            لا يوجد أعضاء مضافون حاليًا.
        </div>
    @endif
</div>
@endsection