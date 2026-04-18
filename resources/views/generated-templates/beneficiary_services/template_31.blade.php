python3 - <<'PY'
from pathlib import Path

path = Path('/var/www/khaier/resources/views/themes/default/pages/beneficiary-services.blade.php')
text = path.read_text()

old = """    .radwan-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 25px;
    }"""

if old in text:
    new = """    .radwan-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 25px !important;
    }"""
    text = text.replace(old, new, 1)
else:
    old2 = """    .radwan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }"""
    new2 = """    .radwan-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 25px !important;
    }"""
    if old2 not in text:
        raise SystemExit('لم أجد تعريف .radwan-grid المتوقع في الملف')
    text = text.replace(old2, new2, 1)

if "@media (max-width: 768px)" in text and ".radwan-grid {\n            grid-template-columns: 1fr;" in text:
    pass
else:
    insert_at = text.rfind("</style>")
    if insert_at == -1:
        raise SystemExit('لم أجد وسم </style> داخل الملف')
    text = text[:insert_at] + """
    @media (max-width: 768px) {
        .radwan-grid {
            grid-template-columns: 1fr !important;
        }
    }

""" + text[insert_at:]

path.write_text(text)
print('Forced beneficiary services grid to 2 columns.')
PY

cd /var/www/khaier && php artisan optimize:clear