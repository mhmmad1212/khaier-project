@php
    $statePath = $getStatePath();
    $current = $getState();

    $icons = [
        'heroicon-o-home' => 'الرئيسية',
        'heroicon-o-building-office' => 'مبنى',
        'heroicon-o-users' => 'مستخدمون',
        'heroicon-o-user-group' => 'مجموعة',
        'heroicon-o-user' => 'مستخدم',
        'heroicon-o-briefcase' => 'حقيبة',
        'heroicon-o-chart-bar' => 'إحصائيات',
        'heroicon-o-presentation-chart-bar' => 'عرض',
        'heroicon-o-document-text' => 'مستند',
        'heroicon-o-newspaper' => 'أخبار',
        'heroicon-o-photo' => 'صورة',
        'heroicon-o-camera' => 'كاميرا',
        'heroicon-o-envelope' => 'بريد',
        'heroicon-o-phone' => 'هاتف',
        'heroicon-o-map-pin' => 'موقع',
        'heroicon-o-globe-alt' => 'عالم',
        'heroicon-o-link' => 'رابط',
        'heroicon-o-hand-raised' => 'شراكة',
        'heroicon-o-heart' => 'قلب',
        'heroicon-o-star' => 'نجمة',
        'heroicon-o-trophy' => 'كأس',
        'heroicon-o-academic-cap' => 'تعليم',
        'heroicon-o-banknotes' => 'أموال',
        'heroicon-o-currency-dollar' => 'عملة',
        'heroicon-o-calendar-days' => 'تقويم',
        'heroicon-o-clock' => 'وقت',
        'heroicon-o-cog-6-tooth' => 'إعدادات',
        'heroicon-o-bars-3-bottom-right' => 'قائمة',
        'heroicon-o-rectangle-stack' => 'طبقات',
        'heroicon-o-folder' => 'مجلد',
        'heroicon-o-folder-open' => 'مجلد مفتوح',
        'heroicon-o-tag' => 'وسم',
        'heroicon-o-megaphone' => 'إعلان',
        'heroicon-o-bell' => 'تنبيه',
        'heroicon-o-check-circle' => 'نجاح',
        'heroicon-o-x-circle' => 'إلغاء',
        'heroicon-o-information-circle' => 'معلومة',
        'heroicon-o-exclamation-circle' => 'تحذير',
        'heroicon-o-shield-check' => 'حماية',
        'heroicon-o-scale' => 'ميزان',
        'heroicon-o-clipboard-document-list' => 'قائمة',
        'heroicon-o-document-chart-bar' => 'تقارير',
        'heroicon-o-building-library' => 'مكتبة',
        'heroicon-o-squares-2x2' => 'مربعات',
    ];
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <input type="hidden" wire:model="{{ $statePath }}">

        <div class="mb-3 flex items-center justify-between">
            <div class="text-sm font-medium text-gray-700">اختر الأيقونة</div>

            @if($current)
                <button
                    type="button"
                    wire:click="$set('{{ $statePath }}', null)"
                    class="rounded-md border border-red-200 bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100"
                >
                    إزالة
                </button>
            @endif
        </div>

        <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6 xl:grid-cols-8">
            @foreach($icons as $iconKey => $iconLabel)
                <button
                    type="button"
                    wire:click="$set('{{ $statePath }}', '{{ $iconKey }}')"
                    title="{{ $iconLabel }}"
                    class="flex h-14 w-full items-center justify-center rounded-lg border transition
                    {{ $current === $iconKey ? 'border-primary-600 bg-primary-50 text-primary-600 ring-2 ring-primary-200' : 'border-gray-200 bg-gray-50 text-gray-700 hover:border-primary-300 hover:bg-primary-50' }}"
                >
                    <x-filament::icon :icon="$iconKey" class="h-6 w-6" />
                </button>
            @endforeach
        </div>

        @if($current)
            <div class="mt-3 flex items-center gap-2 text-xs text-gray-600">
                <span>المحدد:</span>
                <span class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-2 py-1">
                    <x-filament::icon :icon="$current" class="h-4 w-4" />
                    <span>{{ $icons[$current] ?? $current }}</span>
                </span>
            </div>
        @endif
    </div>
</x-dynamic-component>
