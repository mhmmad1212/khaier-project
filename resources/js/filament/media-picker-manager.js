function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        return decodeURIComponent(parts.pop().split(';').shift());
    }
    return '';
}

function formatSize(size) {
    if (!size) return '-';
    if (size < 1024) return `${size} B`;
    if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
    return `${(size / (1024 * 1024)).toFixed(1)} MB`;
}

window.openGlobalMediaPicker = async function (statePath) {
    let overlay = document.getElementById('global-media-picker-overlay');

    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'global-media-picker-overlay';
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
            <div style="background:#fff;width:min(1180px,100%);max-height:92vh;border-radius:18px;overflow:hidden;display:flex;flex-direction:column;">
                <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <div style="font-weight:800;font-size:18px;">مكتبة الوسائط</div>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <button id="gmp-tab-library" type="button" style="background:#127962;color:#fff;border:none;border-radius:10px;padding:10px 14px;cursor:pointer;">المكتبة</button>
                        <button id="gmp-tab-upload" type="button" style="background:#eef2f7;color:#111827;border:none;border-radius:10px;padding:10px 14px;cursor:pointer;">رفع جديد</button>
                        <button id="gmp-close" type="button" style="background:#111827;color:#fff;border:none;border-radius:10px;padding:10px 14px;cursor:pointer;">إغلاق</button>
                    </div>
                </div>

                <div id="gmp-toolbar" style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                    <input id="gmp-search" type="text" placeholder="بحث بالاسم أو الرقم..." style="border:1px solid #d1d5db;border-radius:10px;padding:10px 12px;min-width:240px;">
                </div>

                <div id="gmp-body" style="display:grid;grid-template-columns:360px 1fr;min-height:520px;max-height:calc(92vh - 90px);overflow:hidden;"></div>
            </div>
        `;

        document.body.appendChild(overlay);

        overlay.querySelector('#gmp-close').onclick = () => {
            overlay.style.display = 'none';
        };

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.style.display = 'none';
            }
        });
    }

    overlay.style.display = 'flex';

    const body = overlay.querySelector('#gmp-body');
    const toolbar = overlay.querySelector('#gmp-toolbar');
    const searchInput = overlay.querySelector('#gmp-search');
    const tabLibrary = overlay.querySelector('#gmp-tab-library');
    const tabUpload = overlay.querySelector('#gmp-tab-upload');

    let items = [];

    const selectItem = (item) => {
        window.dispatchEvent(new CustomEvent('media-library-selected', {
            detail: {
                statePath,
                item,
            },
        }));

        overlay.style.display = 'none';
    };

    const renderUsage = async (itemId) => {
        try {
            const response = await fetch(`/admin/media-library/usage/${itemId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            const usage = await response.json();

            const usageWrap = document.getElementById('gmp-usage-list');
            if (!usageWrap) return;

            if (!Array.isArray(usage) || !usage.length) {
                usageWrap.innerHTML = `<div style="font-size:13px;color:#6b7280;">غير مستخدم حاليًا.</div>`;
                return;
            }

            usageWrap.innerHTML = `
                <div style="display:grid;gap:8px;">
                    ${usage.map(entry => `
                        <a href="${entry.url}" style="display:block;padding:10px 12px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;color:#111827;background:#fff;">
                            <div style="font-size:12px;color:#127962;font-weight:700;margin-bottom:4px;">${entry.type}</div>
                            <div style="font-size:14px;">${entry.title}</div>
                        </a>
                    `).join('')}
                </div>
            `;
        } catch (e) {
            const usageWrap = document.getElementById('gmp-usage-list');
            if (usageWrap) {
                usageWrap.innerHTML = `<div style="font-size:13px;color:#dc2626;">تعذر تحميل الاستخدامات.</div>`;
            }
        }
    };

    const renderDetails = (item) => {
        const preview = item.is_image
            ? `<img src="${item.url}" style="width:100%;max-height:320px;object-fit:contain;border-radius:12px;border:1px solid #e5e7eb;background:#fff;">`
            : `<div style="height:220px;border:1px solid #e5e7eb;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#f8fafc;font-size:34px;font-weight:800;color:#dc2626;">PDF</div>`;

        body.querySelector('#gmp-details').innerHTML = `
            <div style="display:grid;gap:14px;">
                ${preview}

                <div>
                    <div style="font-weight:800;font-size:18px;margin-bottom:6px;">${item.title}</div>
                    <div style="font-size:13px;color:#6b7280;">#${item.id}</div>
                </div>

                <div style="display:grid;gap:8px;font-size:14px;color:#374151;">
                    <div><strong>النوع:</strong> ${item.is_image ? 'صورة' : 'PDF'}</div>
                    <div><strong>الامتداد:</strong> ${item.extension || '-'}</div>
                    <div><strong>الحجم:</strong> ${formatSize(item.size)}</div>
                    <div><strong>النص البديل:</strong> ${item.alt_text || '-'}</div>
                </div>

                <div style="padding:14px;border:1px solid #e5e7eb;border-radius:12px;background:#fafafa;">
                    <div style="font-weight:800;margin-bottom:10px;">أماكن الاستخدام</div>
                    <div id="gmp-usage-list" style="font-size:13px;color:#6b7280;">جاري التحميل...</div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button id="gmp-use-item" type="button" style="background:#127962;color:#fff;border:none;border-radius:10px;padding:10px 14px;cursor:pointer;">استخدام هذا الملف</button>
                    <a href="${item.url}" target="_blank" style="background:#eef2f7;color:#111827;border:none;border-radius:10px;padding:10px 14px;text-decoration:none;">فتح الملف</a>
                </div>
            </div>
        `;

        body.querySelector('#gmp-use-item').onclick = () => selectItem(item);
        renderUsage(item.id);
    };

    const renderLibrary = (list = items) => {
        toolbar.style.display = 'flex';
        tabLibrary.style.background = '#127962';
        tabLibrary.style.color = '#fff';
        tabUpload.style.background = '#eef2f7';
        tabUpload.style.color = '#111827';

        body.innerHTML = `
            <div id="gmp-grid" style="overflow:auto;padding:20px;border-left:1px solid #eee;">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:14px;">
                    ${list.map(item => `
                        <button
                            type="button"
                            class="gmp-item"
                            data-id="${item.id}"
                            style="cursor:pointer;border:1px solid #e5e7eb;border-radius:12px;padding:8px;background:#fff;text-align:right;"
                        >
                            ${item.is_image
                                ? `<img src="${item.url}" style="width:100%;height:95px;object-fit:cover;border-radius:8px;border:1px solid #eee;margin-bottom:8px;">`
                                : `<div style="height:95px;border-radius:8px;border:1px solid #eee;margin-bottom:8px;display:flex;align-items:center;justify-content:center;background:#f8fafc;font-weight:800;color:#dc2626;">PDF</div>`
                            }
                            <div style="font-size:12px;font-weight:700;line-height:1.5;">#${item.id}</div>
                            <div style="font-size:11px;color:#6b7280;line-height:1.5;word-break:break-word;">${item.title}</div>
                        </button>
                    `).join('')}
                </div>
            </div>

            <div id="gmp-details" style="padding:24px;">
                <div style="color:#6b7280">اختر ملفًا من المكتبة لعرض تفاصيله.</div>
            </div>
        `;

        body.querySelectorAll('.gmp-item').forEach((btn) => {
            btn.onclick = () => {
                const item = list.find(i => String(i.id) === btn.dataset.id);
                if (item) renderDetails(item);
            };
        });
    };

    const renderUpload = () => {
        toolbar.style.display = 'none';
        tabUpload.style.background = '#127962';
        tabUpload.style.color = '#fff';
        tabLibrary.style.background = '#eef2f7';
        tabLibrary.style.color = '#111827';

        body.innerHTML = `
            <div style="grid-column:1 / -1;overflow:auto;padding:24px;">
                <div style="max-width:680px;margin:0 auto;">
                    <div style="border:1px solid #e5e7eb;border-radius:16px;padding:22px;background:#fafafa;">
                        <div style="font-weight:800;font-size:18px;margin-bottom:16px;">رفع ملف جديد</div>

                        <div style="display:grid;gap:14px;">
                            <input id="gmp-upload-title" type="text" placeholder="عنوان الملف (اختياري)" style="border:1px solid #d1d5db;border-radius:10px;padding:12px;">
                            <input id="gmp-upload-alt" type="text" placeholder="النص البديل (اختياري - للصور)" style="border:1px solid #d1d5db;border-radius:10px;padding:12px;">
                            <input id="gmp-upload-file" type="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf" style="border:1px solid #d1d5db;border-radius:10px;padding:12px;background:#fff;">
                            <button id="gmp-upload-submit" type="button" style="background:#127962;color:#fff;border:none;border-radius:10px;padding:12px 16px;cursor:pointer;">رفع الآن</button>
                            <div id="gmp-upload-status" style="font-size:13px;color:#6b7280;"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const submitBtn = body.querySelector('#gmp-upload-submit');
        const statusEl = body.querySelector('#gmp-upload-status');

        submitBtn.onclick = async () => {
            const fileInput = body.querySelector('#gmp-upload-file');
            const titleInput = body.querySelector('#gmp-upload-title');
            const altInput = body.querySelector('#gmp-upload-alt');

            if (!fileInput.files.length) {
                statusEl.textContent = 'اختر ملفًا أولًا.';
                statusEl.style.color = '#dc2626';
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('title', titleInput.value || '');
            formData.append('alt_text', altInput.value || '');

            const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const xsrfCookie = getCookie('XSRF-TOKEN');

            statusEl.textContent = 'جاري الرفع...';
            statusEl.style.color = '#6b7280';
            submitBtn.disabled = true;

            try {
                const response = await fetch('/admin/media-library/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfMeta,
                        'X-XSRF-TOKEN': xsrfCookie,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                    credentials: 'same-origin',
                });

                const rawText = await response.text();
                let data = {};

                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    data = { message: rawText };
                }

                if (!response.ok || !data.success) {
                    let message = data.message || 'تعذر رفع الملف.';
                    if (data.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        if (firstKey && data.errors[firstKey]?.length) {
                            message = data.errors[firstKey][0];
                        }
                    }
                    throw new Error(message);
                }

                items.unshift(data.item);
                statusEl.textContent = data.duplicate
                    ? 'تم الرفع بنجاح، ويوجد ملف مشابه مرفوع سابقًا.'
                    : 'تم الرفع بنجاح.';
                statusEl.style.color = data.duplicate ? '#b45309' : '#127962';

                renderLibrary(items);
                renderDetails(data.item);
            } catch (error) {
                console.error(error);
                statusEl.textContent = error.message || 'تعذر رفع الملف.';
                statusEl.style.color = '#dc2626';
                submitBtn.disabled = false;
            }
        };
    };

    tabLibrary.onclick = () => renderLibrary(items);
    tabUpload.onclick = () => renderUpload();

    searchInput.value = '';

    try {
        const response = await fetch('/admin/media-library/picker-json', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        });

        items = await response.json();
        renderLibrary(items);

        searchInput.oninput = () => {
            const q = searchInput.value.trim().toLowerCase();

            const filtered = items.filter((item) =>
                String(item.title || '').toLowerCase().includes(q) ||
                String(item.id).includes(q) ||
                String(item.extension || '').toLowerCase().includes(q)
            );

            renderLibrary(filtered);
        };
    } catch (e) {
        body.innerHTML = `<div style="grid-column:1 / -1;padding:30px;text-align:center;color:#dc2626;">تعذر تحميل مكتبة الوسائط.</div>`;
        console.error(e);
    }
};

window.addEventListener('open-media-library', (event) => {
    const statePath = event.detail?.state;
    if (!statePath) return;

    window.openGlobalMediaPicker(statePath);
});
