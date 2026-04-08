@extends('themes.default.layouts.app')

@section('title', $page->title . ' - ' . $association->name)

@section('content')
<section class="page-wrap" style="padding:50px 0 80px;">
    <div class="container">
        <div style="background:linear-gradient(135deg,#127962,#0d5948);border-radius:30px;padding:40px;color:#fff;margin-bottom:30px;box-shadow:0 24px 50px rgba(15,23,42,.13);">
            <h1 style="font-size:2.2rem;font-weight:800;margin:0 0 10px;line-height:1.5;">{{ $page->title }}</h1>
            <p style="color:rgba(255,255,255,.88);line-height:2;margin:0;">استعراض التراخيص والوثائق المرتبطة بها.</p>
        </div>

        <div style="background:#fff;border-radius:26px;border:1px solid rgba(18,121,98,.08);box-shadow:0 18px 40px rgba(15,23,42,.08);overflow:hidden;">
            <div style="padding:28px;">
                @if($items->count())
                    <div style="display:grid;gap:16px;">
                        @foreach($items as $item)
                            <div style="border:1px solid #e5e7eb;border-radius:18px;padding:18px;display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
                                <div>
                                    <div style="font-size:1.1rem;font-weight:800;color:#111827;">{{ $item->title }}</div>
                                </div>

                                @if(!empty($item->file))
                                    <a href="{{ \App\Support\Media\MediaUrl::forDiskPath('public', $item->file) }}"
                                       target="_blank"
                                       style="display:inline-flex;align-items:center;gap:8px;background:#127962;color:#fff;text-decoration:none;padding:10px 16px;border-radius:12px;font-weight:700;">
                                        عرض المرفق
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="padding:18px;border:1px dashed #d1d5db;border-radius:14px;background:#fafafa;color:#6b7280;font-weight:600;">
                        لا توجد تراخيص مضافة حاليًا.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
