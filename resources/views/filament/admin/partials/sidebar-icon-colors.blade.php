<style>
.fi-sidebar-item-icon,
.fi-topbar-item-icon {
    transition: transform .18s ease, filter .18s ease, color .18s ease, opacity .18s ease;
    opacity: .98;
}

.fi-sidebar-item-button:hover .fi-sidebar-item-icon,
.fi-sidebar-item a:hover .fi-sidebar-item-icon {
    transform: translateY(-1px) scale(1.08);
    filter: saturate(1.12);
}

.fi-sidebar-item-active .fi-sidebar-item-icon,
a[aria-current="page"] .fi-sidebar-item-icon {
    filter: drop-shadow(0 4px 10px rgba(0,0,0,.14));
}

/* Dashboard */
a[href$="/admin"] .fi-sidebar-item-icon { color: #7c3aed !important; }

/* Main sections */
a[href*="/admin/news"] .fi-sidebar-item-icon { color: #0ea5e9 !important; }
a[href*="/admin/pages"] .fi-sidebar-item-icon { color: #f59e0b !important; }
a[href*="/admin/employees"] .fi-sidebar-item-icon { color: #10b981 !important; }
a[href*="/admin/departments"] .fi-sidebar-item-icon { color: #6366f1 !important; }
a[href*="/admin/program-projects"] .fi-sidebar-item-icon { color: #ef4444 !important; }
a[href*="/admin/services"] .fi-sidebar-item-icon { color: #14b8a6 !important; }

/* Governance */
a[href*="/admin/board-members"] .fi-sidebar-item-icon { color: #8b5cf6 !important; }
a[href*="/admin/committees"] .fi-sidebar-item-icon { color: #06b6d4 !important; }
a[href*="/admin/general-assembly-members"] .fi-sidebar-item-icon { color: #22c55e !important; }

/* Compliance */
a[href*="/admin/policies"] .fi-sidebar-item-icon { color: #3b82f6 !important; }
a[href*="/admin/regulations"] .fi-sidebar-item-icon { color: #f97316 !important; }
a[href*="/admin/disclosures"] .fi-sidebar-item-icon { color: #ec4899 !important; }
a[href*="/admin/financial-reports"] .fi-sidebar-item-icon { color: #84cc16 !important; }
a[href*="/admin/licenses"] .fi-sidebar-item-icon { color: #eab308 !important; }
a[href*="/admin/association-plans"] .fi-sidebar-item-icon { color: #f43f5e !important; }
a[href*="/admin/site-forms"] .fi-sidebar-item-icon { color: #0f766e !important; }
a[href*="/admin/site-settings"] .fi-sidebar-item-icon { color: #64748b !important; }
</style>
