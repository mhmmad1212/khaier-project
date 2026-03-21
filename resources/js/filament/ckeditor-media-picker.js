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

class MediaLibraryButtonPlugin extends Plugin {
    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add('mediaLibrary', () => {
            const button = new ButtonView();

            button.set({
                label: 'الوسائط',
                withText: true,
                tooltip: true,
            });

            button.on('execute', () => {
                window.openCkMediaLibrary(editor);
            });

            return button;
        });
    }
}

window.openCkMediaLibrary = async function (editor) {
    let overlay = document.getElementById('ck-media-library-overlay');

    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'ck-media-library-overlay';
        overlay.style.cssText = `
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.55);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
        `;

        overlay.innerHTML = `
            <div style="background:#fff;width:min(1100px,100%);max-height:90vh;border-radius:18px;overflow:hidden;display:flex;flex-direction:column;">
                <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <div style="font-weight:800;font-size:18px;">مكتبة الوسائط</div>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <button id="ck-tab-library" type="button" style="background:#127962;color:#fff;border:none;border-radius:10px;padding:10px 14px;cursor:pointer;">المكتبة</button>
                        <button id="ck-tab-upload" type="button" style="background:#eef2f7;color:#111827;border:none;border-radius:10px;padding:10px 14px;cursor:pointer;">رفع جديد</button>
                        <button id="ck-media-library-close" type="button" style="background:#111827;color:#fff;border:none;border-radius:10px;padding:10px 14px;cursor:pointer;">إغلاق</button>
                    </div>
                </div>

                <div id="ck-media-library-toolbar" style="padding:16px 20px;border-bottom:1px solid #f1f5f9;">
                    <input id="ck-media-library-search" type="text" placeholder="بحث..." style="border:1px solid #d1d5db;border-radius:10px;padding:10px 12px;min-width:220px;">
                </div>

                <div id="ck-media-library-body" style="padding:20px;overflow:auto;"></div>
            </div>
        `;

        document.body.appendChild(overlay);

        overlay.querySelector('#ck-media-library-close').addEventListener('click', () => {
            overlay.style.display = 'none';
        });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.style.display = 'none';
            }
        });
    }

    overlay.style.display = 'flex';

    const body = overlay.querySelector('#ck-media-library-body');
    const toolbar = overlay.querySelector('#ck-media-library-toolbar');
    const searchInput = overlay.querySelector('#ck-media-library-search');
    const tabLibrary = overlay.querySelector('#ck-tab-library');
    const tabUpload = overlay.querySelector('#ck-tab-upload');

    let items = Array.isArray(window.ckEditorMediaItems) ? [...window.ckEditorMediaItems] : [];

    const insertImageIntoEditor = (url, title = '') => {
        editor.model.change((writer) => {
            const imageElement = writer.createElement('imageBlock', {
                src: url,
                alt: title,
            });

            editor.model.insertContent(imageElement, editor.model.document.selection);

            const root = editor.model.document.getRoot();
            const paragraph = writer.createElement('paragraph');
            writer.insert(paragraph, writer.createPositionAt(root, 'end'));
            writer.setSelection(paragraph, 'in');
        });

        editor.editing.view.focus();
        overlay.style.display = 'none';
    };

    const renderLibrary = (list = items) => {
        toolbar.style.display = 'block';
        tabLibrary.style.background = '#127962';
        tabLibrary.style.color = '#fff';
        tabUpload.style.background = '#eef2f7';
        tabUpload.style.color = '#111827';

        if (!list.length) {
            body.innerHTML = `<div style="padding:30px;text-align:center;color:#6b7280;">لا توجد صور في مكتبة الوسائط.</div>`;
            return;
        }

        body.innerHTML = `
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:16px;">
                ${list.map(item => `
                    <button
                        type="button"
                        class="ck-media-item"
                        data-url="${item.url}"
                        data-title="${(item.alt_text || item.title || '').replace(/"/g, '&quot;')}"
                        style="border:1px solid #e5e7eb;background:#fff;border-radius:14px;padding:10px;text-align:right;cursor:pointer;transition:.2s;"
                    >
                        <img src="${item.url}" alt="${item.title}" style="width:100%;height:120px;object-fit:cover;border-radius:10px;border:1px solid #eee;display:block;margin-bottom:8px;">
                        <div style="font-size:12px;font-weight:700;line-height:1.6;">#${item.id}</div>
                        <div style="font-size:12px;color:#374151;line-height:1.6;word-break:break-word;">${item.title}</div>
                    </button>
                `).join('')}
            </div>
        `;

        body.querySelectorAll('.ck-media-item').forEach((btn) => {
            btn.addEventListener('click', () => {
                insertImageIntoEditor(btn.dataset.url, btn.dataset.title || '');
            });
        });
    };

    const renderUpload = () => {
        toolbar.style.display = 'none';
        tabUpload.style.background = '#127962';
        tabUpload.style.color = '#fff';
        tabLibrary.style.background = '#eef2f7';
        tabLibrary.style.color = '#111827';

        body.innerHTML = `
            <div style="max-width:620px;margin:0 auto;">
                <div style="border:1px solid #e5e7eb;border-radius:16px;padding:20px;background:#fafafa;">
                    <div style="font-weight:700;margin-bottom:14px;">رفع وسيط جديد</div>

                    <div style="display:grid;gap:14px;">
                        <input id="ck-upload-title" type="text" placeholder="عنوان الصورة (اختياري)" style="border:1px solid #d1d5db;border-radius:10px;padding:12px;">
                        <input id="ck-upload-alt" type="text" placeholder="النص البديل (اختياري)" style="border:1px solid #d1d5db;border-radius:10px;padding:12px;">
                        <input id="ck-upload-file" type="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf" style="border:1px solid #d1d5db;border-radius:10px;padding:12px;background:#fff;">
                        <button id="ck-upload-submit" type="button" style="background:#127962;color:#fff;border:none;border-radius:10px;padding:12px 16px;cursor:pointer;">رفع الآن</button>
                        <div id="ck-upload-status" style="font-size:13px;color:#6b7280;"></div>
                    </div>
                </div>
            </div>
        `;

        const submitBtn = body.querySelector('#ck-upload-submit');
        const statusEl = body.querySelector('#ck-upload-status');

        submitBtn.addEventListener('click', async () => {
            const fileInput = body.querySelector('#ck-upload-file');
            const titleInput = body.querySelector('#ck-upload-title');
            const altInput = body.querySelector('#ck-upload-alt');

            if (!fileInput.files.length) {
                statusEl.textContent = 'اختر ملفًا أولًا.';
                statusEl.style.color = '#dc2626';
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('title', titleInput.value || '');
            formData.append('alt_text', altInput.value || '');

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            statusEl.textContent = 'جاري الرفع...';
            statusEl.style.color = '#6b7280';
            submitBtn.disabled = true;

            try {
                const response = await fetch('/admin/media-library/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                    credentials: 'same-origin',
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'تعذر رفع الملف.');
                }

                items.unshift(data.item);
                window.ckEditorMediaItems = items;

                renderLibrary(items);
                insertImageIntoEditor(data.item.url, data.item.alt_text || data.item.title || '');
            } catch (error) {
                console.error(error);
                statusEl.textContent = error.message || 'تعذر رفع الملف.';
                statusEl.style.color = '#dc2626';
                submitBtn.disabled = false;
            }
        });
    };

    tabLibrary.onclick = () => renderLibrary(items);
    tabUpload.onclick = () => renderUpload();

    renderLibrary(items);

    searchInput.value = '';
    searchInput.oninput = () => {
        const q = searchInput.value.trim().toLowerCase();

        const filtered = items.filter((item) =>
            String(item.title || '').toLowerCase().includes(q) ||
            String(item.id).includes(q)
        );

        renderLibrary(filtered);
    };
};

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
