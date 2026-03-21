@php
$record = $getRecord();
@endphp

@if($record && $record->exists && $record->image)

<div style="margin-bottom:15px">

    <div style="font-weight:bold;margin-bottom:10px">
        الصورة الحالية
    </div>

    <img
        src="{{ asset('storage/'.$record->image) }}"
        style="width:200px;border-radius:10px;border:1px solid #ddd;margin-bottom:10px"
    >

    <form method="POST" action="/admin/delete-news-image/{{ $record->id }}">
        @csrf
        <button
            type="submit"
            style="
            background:#ef4444;
            color:white;
            border:none;
            padding:8px 14px;
            border-radius:6px;
            cursor:pointer;
            "
        >
        حذف الصورة
        </button>
    </form>

</div>

@endif
