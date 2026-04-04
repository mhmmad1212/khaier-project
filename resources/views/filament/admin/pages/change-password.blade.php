<x-filament-panels::page>
    <div class="max-w-2xl">
        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit">
                    حفظ كلمة المرور الجديدة
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
