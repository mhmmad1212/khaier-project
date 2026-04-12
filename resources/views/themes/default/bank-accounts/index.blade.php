@extends('themes.default.layouts.app')

@section('content')
@php
    $bankAccounts = collect($bankAccounts ?? ($items ?? []));
@endphp

<div style="max-width:1200px; margin:40px auto; padding:0 16px; direction:rtl; text-align:right;">
    <div style="margin-bottom:28px;">
        <h1 style="margin:0 0 10px; font-size:32px; font-weight:800; color:#111827;">
            {{ $page->title ?? 'الحسابات البنكية' }}
        </h1>

        @if(!empty($page->excerpt))
            <div style="color:#6b7280; font-size:16px; line-height:1.9;">
                {{ $page->excerpt }}
            </div>
        @endif
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:24px;">
        @forelse($bankAccounts as $bankAccount)
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:20px; padding:24px;">
                <h3 style="margin:0 0 8px; font-size:22px; font-weight:800;">{{ $bankAccount->name }}</h3>
                <div style="color:#6b7280; margin-bottom:10px;">{{ $bankAccount->bank_name }}</div>
                <div style="font-size:18px; font-weight:800;">{{ $bankAccount->account_number }}</div>
            </div>
        @empty
            <div style="grid-column:1/-1; text-align:center; background:#fff; border:1px dashed #cbd5e1; color:#6b7280; border-radius:20px; padding:48px 20px;">
                لا توجد حسابات بنكية مضافة حاليًا.
            </div>
        @endforelse
    </div>
</div>
@endsection
