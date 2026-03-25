@extends('themes.default.layouts.app')

@section('content')
@php
    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#2ea36b';

    $employeesList = collect($items ?? ($employees ?? []));
@endphp

<div style="direction: rtl; text-align: right; max-width: 1200px; margin: 40px auto; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-sizing: border-box;">

    <style>
        .employees-header {
            border-bottom: 2px solid {{ $buttonColor }};
            padding-bottom: 15px;
            margin-bottom: 35px;
        }

        .employees-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .employee-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .employee-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.10);
        }

        .employee-image-wrap {
            position: relative;
            height: 290px;
            overflow: hidden;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        }

        .employee-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s ease;
            display: block;
        }

        .employee-card:hover .employee-image {
            transform: scale(1.06);
        }

        .employee-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 15px;
            font-weight: 600;
        }

        .employee-body {
            padding: 20px 18px 18px;
        }

        .employee-name {
            font-size: 20px;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 8px;
            line-height: 1.6;
        }

        .employee-position {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(46, 163, 107, 0.10);
            color: {{ $buttonColor }};
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .employee-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 6px;
        }

        .employee-meta-item {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.8;
        }

        .employee-meta-label {
            font-weight: 700;
            color: #111827;
        }

        .employee-actions {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .employee-link {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: opacity .2s ease;
        }

        .employee-link:hover {
            opacity: .88;
        }

        .employee-link-primary {
            background: {{ $buttonColor }};
            color: #fff;
            border: 1px solid {{ $buttonColor }};
        }

        .employee-link-secondary {
            background: #fff;
            color: {{ $buttonColor }};
            border: 1px solid {{ $buttonColor }};
        }

        .employees-empty {
            text-align: center;
            padding: 70px 20px;
            background: #f9fafb;
            border: 2px dashed #cbd5e0;
            border-radius: 14px;
            color: #6b7280;
            font-size: 18px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .employee-image-wrap {
                height: 250px;
            }

            .employee-name {
                font-size: 18px;
            }
        }
    </style>

    <div class="employees-header">
        <h1 style="color: #1a4a38; font-size: 30px; font-weight: 800; margin: 0 0 8px;">الموظفون</h1>
        <p style="margin: 0; color: #6b7280; font-size: 15px;">
            تعرف على فريق العمل والكوادر الإدارية والتنفيذية.
        </p>
    </div>

    @if($employeesList->count())
        <div class="employees-grid">
            @foreach($employeesList as $item)
                @php
                    $photo = $item->photo
                        ?? ($item->photoMedia->file ?? null)
                        ?? ($item->image ?? null);

                    $name = $item->name ?? $item->title ?? 'بدون اسم';
                    $position = $item->position ?? $item->job_title ?? null;
                    $department = $item->department_name ?? ($item->department->name ?? null);
                    $phone = $item->phone ?? null;
                    $email = $item->email ?? null;
                @endphp

                <div class="employee-card">
                    <div class="employee-image-wrap">
                        @if(!empty($photo))
                            <img loading="lazy" decoding="async"
                                src="{{ asset('storage/' . ltrim($photo, '/')) }}"
                                alt="{{ $name }}"
                                class="employee-image"
                            >
                        @else
                            <div class="employee-placeholder">
                                لا توجد صورة
                            </div>
                        @endif
                    </div>

                    <div class="employee-body">
                        <h2 class="employee-name">{{ $name }}</h2>

                        @if(!empty($position))
                            <div class="employee-position">{{ $position }}</div>
                        @endif

                        <div class="employee-meta">
                            @if(!empty($department))
                                <div class="employee-meta-item">
                                    <span class="employee-meta-label">القسم:</span>
                                    {{ $department }}
                                </div>
                            @endif

                            @if(!empty($phone))
                                <div class="employee-meta-item">
                                    <span class="employee-meta-label">الهاتف:</span>
                                    {{ $phone }}
                                </div>
                            @endif

                            @if(!empty($email))
                                <div class="employee-meta-item">
                                    <span class="employee-meta-label">البريد:</span>
                                    {{ $email }}
                                </div>
                            @endif
                        </div>

                        @if(!empty($phone) || !empty($email))
                            <div class="employee-actions">
                                @if(!empty($phone))
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $phone) }}"
                                       target="_blank"
                                       class="employee-link employee-link-primary">
                                        مراسلة واتساب
                                    </a>
                                @endif

                                @if(!empty($email))
                                    <a href="mailto:{{ $email }}"
                                       class="employee-link employee-link-secondary">
                                        مراسلة بالإيميل
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="employees-empty">
            لا يوجد موظفون مضافون حاليًا.
        </div>
    @endif
</div>
@endsection