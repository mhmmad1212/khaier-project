@php
    $statePath = $getStatePath();
    $selectedId = \App\Support\UnifiedMediaPicker::selectedId(request(), $statePath, $getState());
    $media = \App\Support\UnifiedMediaPicker::selectedMedia(request(), $statePath, $getState());
    $pickerUrl = \App\Support\UnifiedMediaPicker::buildPickerUrl(request(), $statePath);
    $storageKey = \App\Support\UnifiedMediaPicker::storageKey(request(), $statePath);
    $plainStatePath = str_replace('data.', '', $statePath);
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            storageKey: '{{ $storageKey }}',
            statePath: '{{ $plainStatePath }}',

            selectedId: @js($selectedId),
            selectedFile: @js($media?->file),
            selectedTitle: @js($media?->title ?: ($media ? basename((string) $media->file) : null)),
            selectedExtension: @js($media?->extension ?: null),
            selectedIsImage: @js((bool) ($media?->is_image)),
            selectedAlt: @js($media?->alt_text ?: $media?->title),
            selectedMediaPublicUrl: @js($media?->url),

            init() {
                this.restoreState();
                this.applySelectedMediaFromUrl();
                this.syncToWire();
            },

            shouldSkipKey(key) {
                if (!key) return true;

                const mediaExact = [
                    'logo',
                    'favicon',
                    'file',
                    'image',
                    'photo',
                    'cover_image',
                    'featured_image',
                    'attachment',
                    'report_file',
                    'report',
                    'icon'
                ];

                if (key.endsWith('_media_id')) return true;
                if (mediaExact.includes(key)) return true;

                return false;
            },

            saveState() {
                const data = $wire.data || {};
                const filtered = {};

                for (const [key, value] of Object.entries(data)) {
                    if (!this.shouldSkipKey(key)) {
                        filtered[key] = value;
                    }
                }

                sessionStorage.setItem(this.storageKey, JSON.stringify(filtered));
            },

            restoreState() {
                const urlParams = new URLSearchParams(window.location.search);
                const hasReturnParams = urlParams.has('selected_media_id');

                if (!hasReturnParams) return;

                const raw = sessionStorage.getItem(this.storageKey);
                if (!raw) return;

                try {
                    const payload = JSON.parse(raw);

                    if (!$wire.data) {
                        $wire.data = {};
                    }

                    for (const [key, value] of Object.entries(payload)) {
                        if (!this.shouldSkipKey(key)) {
                            $wire.data[key] = value;
                        }
                    }

                    sessionStorage.removeItem(this.storageKey);
                } catch (e) {
                    console.error('فشل استعادة البيانات', e);
                }
            },

            applySelectedMediaFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);

                const selectedField = urlParams.get('selected_media_field');
                const selectedId = urlParams.get('selected_media_id');
                const selectedFile = urlParams.get('selected_media_file');
                const selectedUrl = urlParams.get('selected_media_url');

                if (!selectedField || !selectedId) return;
                if (selectedField !== '{{ $statePath }}') return;

                this.selectedId = selectedId;
                this.selectedFile = selectedFile;
                this.selectedMediaPublicUrl = selectedUrl || null;
                this.selectedTitle = selectedFile ? selectedFile.split('/').pop() : 'ملف مختار';
                this.selectedExtension = selectedFile && selectedFile.includes('.') ? selectedFile.split('.').pop() : 'FILE';

                const ext = (this.selectedExtension || '').toLowerCase();
                this.selectedIsImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext);

                this.syncToWire();
            },

            syncToWire() {
                if (!$wire.data) {
                    $wire.data = {};
                }

                $wire.data[this.statePath.replace('data.', '')] = this.selectedId;
            },

            clearMedia() {
                this.selectedId = null;
                this.selectedFile = null;
                this.selectedTitle = null;
                this.selectedExtension = null;
                this.selectedIsImage = false;
                this.selectedAlt = null;
                this.selectedMediaPublicUrl = null;

                this.syncToWire();
            }
        }"
        class="space-y-4"
    >
        <input type="hidden" wire:model="{{ $statePath }}" :value="selectedId ?? ''">

        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <a
                href="{{ $pickerUrl }}"
                @click="saveState()"
                style="display:inline-flex;align-items:center;gap:8px;background:#127962;color:#fff;text-decoration:none;padding:10px 16px;border-radius:12px;font-weight:700;"
            >
                <span x-text="selectedId ? 'تغيير الملف' : 'اختيار ملف من مكتبة الوسائط'"></span>
            </a>

            <template x-if="selectedId">
                <button
                    type="button"
                    @click="clearMedia()"
                    style="display:inline-flex;align-items:center;gap:8px;background:#b91c1c;color:#fff;text-decoration:none;padding:10px 16px;border-radius:12px;font-weight:700;border:none;cursor:pointer;"
                >
                    إزالة
                </button>
            </template>
        </div>

        <template x-if="selectedId">
            <div style="border:1px solid #e5e7eb;border-radius:16px;background:#fff;overflow:hidden;">
                <div style="padding:16px;display:grid;grid-template-columns:140px 1fr;gap:16px;align-items:start;">
                    <div>
                        <template x-if="selectedIsImage && selectedMediaPublicUrl">
                            <img
                                :src="selectedMediaPublicUrl"
                                :alt="selectedAlt || selectedTitle || 'media'"
                                style="width:140px;height:140px;object-fit:cover;border-radius:14px;border:1px solid #e5e7eb;background:#fafafa;"
                            >
                        </template>

                        <template x-if="!selectedIsImage">
                            <div style="width:140px;height:140px;display:flex;align-items:center;justify-content:center;border-radius:14px;border:1px solid #e5e7eb;background:#fafafa;color:#dc2626;font-weight:800;font-size:28px;">
                                FILE
                            </div>
                        </template>
                    </div>

                    <div style="display:grid;gap:10px;">
                        <div>
                            <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">الملف المختار</div>
                            <div style="font-size:18px;font-weight:800;color:#111827;" x-text="selectedTitle || 'ملف مختار'"></div>
                        </div>

                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <span style="background:#f3f4f6;color:#111827;padding:8px 12px;border-radius:999px;font-size:13px;font-weight:700;">
                                #<span x-text="selectedId"></span>
                            </span>

                            <span style="background:#f3f4f6;color:#111827;padding:8px 12px;border-radius:999px;font-size:13px;font-weight:700;" x-text="(selectedExtension || 'FILE').toUpperCase()"></span>
                        </div>

                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <template x-if="selectedMediaPublicUrl">
                                <a
                                    :href="selectedMediaPublicUrl"
                                    target="_blank"
                                    style="display:inline-flex;align-items:center;gap:8px;background:#eef2f7;color:#111827;text-decoration:none;padding:10px 14px;border-radius:12px;font-weight:700;"
                                >
                                    فتح الملف
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="!selectedId">
            <div style="padding:14px 16px;border:1px dashed #d1d5db;border-radius:14px;background:#fafafa;color:#6b7280;font-weight:600;">
                لم يتم اختيار ملف بعد.
            </div>
        </template>
    </div>
</x-dynamic-component>
