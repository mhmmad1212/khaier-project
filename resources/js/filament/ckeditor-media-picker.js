import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Heading,
    Link,
    List,
    BlockQuote,
    Table,
    TableToolbar,
    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    Code,
    CodeBlock,
    HorizontalLine,
    Font,
    Alignment,
    Undo,
    Plugin,
    ButtonView,
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function moveSelectionToSafeInsertPoint(editor) {
    editor.model.change((writer) => {
        const selection = editor.model.document.selection;
        const selectedElement = selection.getSelectedElement();

        if (selectedElement) {
            writer.setSelection(writer.createPositionAfter(selectedElement));
            return;
        }

        const lastPosition = selection.getLastPosition();

        if (lastPosition) {
            writer.setSelection(lastPosition);
            return;
        }

        const root = editor.model.document.getRoot();
        writer.setSelection(writer.createPositionAt(root, 'end'));
    });
}

function insertHtmlIntoEditor(editor, html) {
    moveSelectionToSafeInsertPoint(editor);

    const viewFragment = editor.data.processor.toView(html);
    const modelFragment = editor.data.toModel(viewFragment);

    editor.model.change((writer) => {
        const insertedRange = editor.model.insertContent(
            modelFragment,
            editor.model.document.selection
        );

        if (insertedRange) {
            writer.setSelection(insertedRange.end);
        } else {
            const root = editor.model.document.getRoot();
            writer.setSelection(writer.createPositionAt(root, 'end'));
        }
    });

    editor.editing.view.focus();
}

function insertMediaIntoEditor(editor, item) {
    if (!editor || !item || !item.url) {
        return;
    }

    const isImage =
        !!item.is_image ||
        /\.(jpg|jpeg|png|gif|webp|bmp|svg|avif)(\?.*)?$/i.test(item.url || '') ||
        /\.(jpg|jpeg|png|gif|webp|bmp|svg|avif)$/i.test(item.file || '');

    const safeUrl = escapeHtml(item.url);
    const safeAlt = escapeHtml(item.alt_text || item.title || '');
    const safeTitle = escapeHtml(item.title || 'تحميل الملف');

    if (isImage) {
        insertHtmlIntoEditor(
            editor,
            `<figure class="image"><img src="${safeUrl}" alt="${safeAlt}"></figure><p>&nbsp;</p>`
        );
        return;
    }

    insertHtmlIntoEditor(
        editor,
        `<p><a href="${safeUrl}" target="_blank" rel="noopener noreferrer">${safeTitle}</a></p><p>&nbsp;</p>`
    );
}

class MediaLibraryButtonPlugin extends Plugin {
    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add('mediaLibrary', () => {
            const button = new ButtonView();

            button.set({
                label: 'صورة',
                withText: true,
                tooltip: true,
            });

            button.on('execute', () => {
                window.__activeKhaierCkEditor = editor;

                const pickerUrl = `/admin/media-picker?field=__ckeditor_content__&return=${encodeURIComponent(window.location.href)}&tab=library`;
                const popup = window.open(
                    pickerUrl,
                    'khaierCkMediaPicker',
                    'width=1400,height=900,resizable=yes,scrollbars=yes'
                );

                if (popup) {
                    popup.focus();
                }
            });

            return button;
        });
    }
}

window.addEventListener('message', (event) => {
    if (event.origin !== window.location.origin) {
        return;
    }

    if (event.data?.type !== 'khaier:ckeditor-media-selected') {
        return;
    }

    insertMediaIntoEditor(window.__activeKhaierCkEditor, event.data.item);
});

async function initSingleCkEditor(element) {
    if (!element || element.dataset.ckeditorInitialized === '1') {
        return;
    }

    const textarea = element.querySelector('textarea');
    if (!textarea) {
        return;
    }

    const editor = await ClassicEditor.create(textarea, {
        licenseKey: 'GPL',
        plugins: [
            Essentials,
            Paragraph,
            Bold,
            Italic,
            Underline,
            Strikethrough,
            Heading,
            Link,
            List,
            BlockQuote,
            Table,
            TableToolbar,
            Image,
            ImageToolbar,
            ImageCaption,
            ImageStyle,
            ImageResize,
            Code,
            CodeBlock,
            HorizontalLine,
            Font,
            Alignment,
            Undo,
            MediaLibraryButtonPlugin,
        ],
        toolbar: [
            'undo', 'redo',
            '|',
            'heading',
            '|',
            'bold', 'italic', 'underline', 'strikethrough',
            '|',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor',
            '|',
            'alignment',
            '|',
            'bulletedList', 'numberedList',
            '|',
            'link', 'blockQuote', 'insertTable', 'horizontalLine',
            '|',
            'code', 'codeBlock',
            '|',
            'mediaLibrary',
        ],
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
        },
        image: {
            toolbar: [
                'imageTextAlternative',
                'toggleImageCaption',
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side',
                'resizeImage',
            ],
        },
        language: 'ar',
    });

    editor.model.document.on('change:data', () => {
        const data = editor.getData();
        textarea.value = data;
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
        textarea.dispatchEvent(new Event('change', { bubbles: true }));
    });

    element.dataset.ckeditorInitialized = '1';
    element.__ckeditorInstance = editor;
}

window.initCkEditorMediaPicker = async function (element) {
    await initSingleCkEditor(element);
};

window.scanAndInitCkEditors = async function () {
    const editors = document.querySelectorAll('[data-ckeditor-wrapper]');
    for (const el of editors) {
        await initSingleCkEditor(el);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        window.scanAndInitCkEditors();
    }, 100);
});

document.addEventListener('livewire:init', () => {
    if (window.Livewire && typeof window.Livewire.hook === 'function') {
        window.Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                window.scanAndInitCkEditors();
            }, 50);
        });
    }
});
