@if(!empty($getState()))
    <div style="display:flex;align-items:center;gap:8px;">
        <div style="width:34px;height:34px;border:1px solid #e5e7eb;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;">
            <i class="{{ $getState() }}"></i>
        </div>
        <span style="font-size:12px;color:#6b7280;">{{ $getState() }}</span>
    </div>
@else
    <span style="color:#9ca3af;">-</span>
@endif
