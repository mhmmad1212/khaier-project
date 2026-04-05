import tinymce from 'tinymce/tinymce';

import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/models/dom';

import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/code';
import 'tinymce/plugins/image';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/directionality';

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function syncEditorToTextarea(editor) {
    const textarea = editor.targetElm;
    if (!textarea) return;

    const content = editor.getContent();
    textarea.value = content;
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    textarea.dispatchEvent(new Event('change', { bubbles: true }));
}

function openTinyPicker(editor, mode = 'images') {
    const allItems = Array.isArray(window.tinyEditorMediaItems) ? [...window.tinyEditorMediaItems] : [];
    const items = mode === 'files'
        ? allItems.filter(item => !item.is_image)
        : allItems.filter(item => item.is_image);

    let overlay = document.getElementById('tiny-media-picker-overlay');
    if (overlay) overlay.remove();

    overlay = document.createElement('div');
    overlay.id = 'tiny-media-picker-overlay';
    overlay.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.55);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    `;

    const title = mode === 'files' ? 'إرفاق ملف / PDF' : 'مكتبة الوسائط';

    overlay.innerHTML = `
        <div style="background:#fff;width:min(1100px,100%);max-height:90vh;border-radius:18px;overflow:hidden;display:flex;flex-direction:column;">
            <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <div style="font-weight:800;font-size:18px;">${title}</div>
                <div style="display:flex;gap:10px;align-items:center;">
                    <button id="tiny-picker-close" type="button" style="background:#111827;color:#fff;border:none;border-radius:10px;padding:10px 14px;cursor:pointer;">إغلاق</button>
                </div>
            </div>

            <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;">
                <input id="tiny-picker-search" type="text" placeholder="بحث..." style="border:1px solid #d1d5db;border-radius:10px;padding:10px 12px;min-width:220px;width:100%;">
            </div>

            <div id="tiny-picker-body" style="padding:20px;overflow:auto;"></div>
        </div>
    `;

    document.body.appendChild(overlay);

    const body = overlay.querySelector('#tiny-picker-body');
    const searchInput = overlay.querySelector('#tiny-picker-search');
    const closeBtn = overlay.querySelector('#tiny-picker-close');

    const insertSelected = (item) => {
        const current = editor.getContent() || '';
        let appended = '';

        if (mode === 'files') {
            const text = escapeHtml(item.title || item.file || 'تحميل الملف');
            appended = `<p><a href="${escapeHtml(item.url)}" target="_blank" rel="noopener">${text}</a></p>`;
        } else {
            appended = `<p><img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.alt_text || item.title || '')}" /></p>`;
        }

        editor.setContent(current + appended);
        editor.save();
        syncEditorToTextarea(editor);
        overlay.remove();
    };

    const render = (list) => {
        if (!list.length) {
            body.innerHTML = `<div style="padding:30px;text-align:center;color:#6b7280;">لا توجد عناصر متاحة.</div>`;
            return;
        }

        if (mode === 'files') {
            body.innerHTML = `
                <div style="display:grid;gap:12px;">
                    ${list.map(item => `
                        <button
                            type="button"
                            class="tiny-file-item"
                            data-id="${item.id}"
                            style="border:1px solid #e5e7eb;background:#fff;border-radius:14px;padding:14px;text-align:right;cursor:pointer;"
                        >
                            <div style="font-size:14px;font-weight:800;color:#111827;">${escapeHtml(item.title)}</div>
                            <div style="font-size:12px;color:#6b7280;margin-top:6px;">.${escapeHtml(item.extension || 'file')}</div>
                        </button>
                    `).join('')}
                </div>
            `;

            body.querySelectorAll('.tiny-file-item').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const item = list.find(x => String(x.id) === String(btn.dataset.id));
                    if (item) insertSelected(item);
                });
            });
            return;
        }

        body.innerHTML = `
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:16px;">
                ${list.map(item => `
                    <button
                        type="button"
                        class="tiny-media-item"
                        data-id="${item.id}"
                        style="border:1px solid #e5e7eb;background:#fff;border-radius:14px;padding:10px;text-align:right;cursor:pointer;"
                    >
                        <img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.title)}" style="width:100%;height:120px;object-fit:cover;border-radius:10px;border:1px solid #eee;display:block;margin-bottom:8px;">
                        <div style="font-size:12px;font-weight:700;line-height:1.6;">#${item.id}</div>
                        <div style="font-size:12px;color:#374151;line-height:1.6;word-break:break-word;">${escapeHtml(item.title)}</div>
                    </button>
                `).join('')}
            </div>
        `;

        body.querySelectorAll('.tiny-media-item').forEach((btn) => {
            btn.addEventListener('click', () => {
                const item = list.find(x => String(x.id) === String(btn.dataset.id));
                if (item) insertSelected(item);
            });
        });
    };

    closeBtn.addEventListener('click', () => overlay.remove());
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.remove();
    });

    render(items);

    searchInput.addEventListener('input', () => {
        const q = searchInput.value.trim().toLowerCase();
        const filtered = items.filter(item =>
            String(item.title || '').toLowerCase().includes(q) ||
            String(item.file || '').toLowerCase().includes(q) ||
            String(item.extension || '').toLowerCase().includes(q) ||
            String(item.id).includes(q)
        );
        render(filtered);
    });
}

async function initSingleTinyEditor(element) {
    if (!element || element.dataset.tinyInitialized === '1') return;

    const textarea = element.querySelector('textarea');
    if (!textarea) return;

    textarea.style.display = 'block';

    const existing = tinymce.get(textarea.id);
    if (existing) {
        existing.remove();
    }

    if (!textarea.id) {
        textarea.id = 'tiny-' + Math.random().toString(36).slice(2);
    }

    await tinymce.init({
        target: textarea,
        height: 520,
        directionality: 'rtl',
        license_key: 'gpl',
        base_url: '/build/tinymce',
        suffix: '.min',
        menubar: false,
        branding: false,
        plugins: 'link lists table code image advlist autolink charmap preview searchreplace visualblocks fullscreen insertdatetime wordcount directionality',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignright aligncenter alignleft | bullist numlist | link table | code preview fullscreen | mediaLibrary fileLibrary',
        content_style: 'body { font-family: Tahoma, Arial, sans-serif; font-size: 16px; direction: rtl; }',
        setup: function (ed) {
            ed.ui.registry.addButton('mediaLibrary', {
                text: 'الوسائط',
                onAction: function () {
                    openTinyPicker(ed, 'images');
                }
            });

            ed.ui.registry.addButton('fileLibrary', {
                text: 'إرفاق ملف',
                onAction: function () {
                    openTinyPicker(ed, 'files');
                }
            });

            ed.on('init', () => syncEditorToTextarea(ed));
            ed.on('change keyup setcontent undo redo', () => syncEditorToTextarea(ed));
        }
    });

    element.dataset.tinyInitialized = '1';
}

window.initTinyEditor = async function (element) {
    await initSingleTinyEditor(element);
};

window.scanAndInitTinyEditors = async function () {
    const editors = document.querySelectorAll('[data-tiny-editor-wrapper]');
    for (const el of editors) {
        await initSingleTinyEditor(el);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        window.scanAndInitTinyEditors();
    }, 100);
});

document.addEventListener('livewire:init', () => {
    if (window.Livewire && typeof window.Livewire.hook === 'function') {
        window.Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                window.scanAndInitTinyEditors();
            }, 50);
        });
    }
});
