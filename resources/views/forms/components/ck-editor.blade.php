<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        data-ckeditor-wrapper
        x-init="setTimeout(() => window.initCkEditorMediaPicker($el), 100)"
        wire:key="ckeditor-{{ md5($getStatePath()) }}"
    >
        <textarea wire:model.defer="{{ $getStatePath() }}" style="min-height: 360px;">{!! $getState() !!}</textarea>
    </div>
</x-dynamic-component>
