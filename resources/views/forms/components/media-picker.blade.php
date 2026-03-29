@php
    $statePath = $getStatePath();
    $selectedId = \App\Support\UnifiedMediaPicker::selectedId(request(), $statePath, $getState());
    $media = \App\Support\UnifiedMediaPicker::selectedMedia(request(), $statePath, $getState());
    $pickerUrl = \App\Support\UnifiedMediaPicker::buildPickerUrl(request(), $statePath);
    $clearUrl = \App\Support\UnifiedMediaPicker::buildClearUrl(request(), $statePath);
    $storageKey = \App\Support\UnifiedMediaPicker::storageKey(request(), $statePath);
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            storageKey: '{{ $storageKey }}',
            init() {
                this.restoreState();
            },
            saveState() {
                // التقاط البيانات مباشرة من عقل Livewire بدلاً من HTML
                const data = $wire.data;
                sessionStorage.setItem(this.storageKey, JSON.stringify(data));
            },
            restoreState() {
                const hasReturnParams = window.location.search.includes('selected_media_id=');
                if (!hasReturnParams) return;

                const raw = sessionStorage.getItem(this.storageKey);
                if (!raw) return;

                try {
                    const payload = JSON.parse(raw);
                    const urlParams = new URLSearchParams(window.location.search);
                    const selectedFieldPath = urlParams.get('selected_media_field') || '';
                    const selectedField = selectedFieldPath.replace('data.', '');
                    const relatedField = selectedField.replace('_media_id', '');

                    if (!$wire.data) $wire.data = {};

                    // إعادة حقن البيانات في قلب Livewire بذكاء
                    for (const [key, value] of Object.entries(payload)) {
                        // نتجاهل حقل الصورة عشان ما نكتب فوق الصورة الجديدة اللي اخترناها
                        if (key !== selectedField && key !== relatedField) {
                            $wire.data[key] = value;
                        }
                    }
                    sessionStorage.removeItem(this.storageKey);
                } catch (e) {
                    console.error('فشل استعادة البيانات', e);
                }
            }
        }"
        class="space-y-4"
    >
        <input type="hidden" wire:model="{{ $statePath }}" value="{{ $selectedId }}">
        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <a
                href="{{ $pickerUrl }}"
                @click="saveState()"
                style="display:inline-flex;align-items:center;gap:8px;background:#127962;color:#fff;text-decoration:none;padding:10px 16px;border-radius:12px;font-weight:700;"
            >
                {{ $media ? 'تغيير الملف' : 'اختيار ملف من مكتبة الوسائط' }}
            </a>
            @if($media)
                <a
                    href="{{ $clearUrl }}"
                    @click="saveState()"
                    style="display:inline-flex;align-items:center;gap:8px;background:#b91c1c;color:#fff;text-decoration:none;padding:10px 16px;border-radius:12px;font-weight:700;"
                >
                    إزالة
                </a>
            @endif
        </div>
        @if($media)
            <div style="border:1px solid #e5e7eb;border-radius:16px;background:#fff;overflow:hidden;">
                <div style="padding:16px;display:grid;grid-template-columns:140px 1fr;gap:16px;align-items:start;">
                    <div>
                        @if($media->is_image)
                            <img src="{{ asset('storage/' . $media->file) }}" alt="{{ $media->alt_text ?: $media->title }}" style="width:140px;height:140px;object-fit:cover;border-radius:14px;border:1px solid #e5e7eb;background:#fafafa;">
                        @else
                            <div style="width:140px;height:140px;display:flex;align-items:center;justify-content:center;border-radius:14px;border:1px solid #e5e7eb;background:#fafafa;color:#dc2626;font-weight:800;font-size:28px;">
                                FILE
                            </div>
                        @endif
                    </div>
                    <div style="display:grid;gap:10px;">
                        <div>
                            <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">الملف المختار</div>
                            <div style="font-size:18px;font-weight:800;color:#111827;">
                                {{ $media->title ?: basename((string) $media->file) }}
                            </div>
                        </div>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <span style="background:#f3f4f6;color:#111827;padding:8px 12px;border-radius:999px;font-size:13px;font-weight:700;">
                                #{{ $media->id }}
                            </span>
                            <span style="background:#f3f4f6;color:#111827;padding:8px 12px;border-radius:999px;font-size:13px;font-weight:700;">
                                {{ strtoupper($media->extension ?: 'FILE') }}
                            </span>
                        </div>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <a href="{{ asset('storage/' . $media->file) }}" target="_blank" style="display:inline-flex;align-items:center;gap:8px;background:#eef2f7;color:#111827;text-decoration:none;padding:10px 14px;border-radius:12px;font-weight:700;">
                                فتح الملف
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div style="padding:14px 16px;border:1px dashed #d1d5db;border-radius:14px;background:#fafafa;color:#6b7280;font-weight:600;">
                لم يتم اختيار ملف بعد.
            </div>
        @endif
    </div>
</x-dynamic-component>
