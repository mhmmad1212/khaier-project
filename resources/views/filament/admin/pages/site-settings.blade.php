<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-2xl border bg-white p-6">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button wire:click="save">
                    حفظ الإعدادات
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
