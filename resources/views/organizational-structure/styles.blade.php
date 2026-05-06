<style>
/* ════════════════════════════════════════════════════════════════
   Organizational Structure – Scoped CSS
   All selectors prefixed with .os- to avoid dashboard conflicts
════════════════════════════════════════════════════════════════ */

/* ── Variables ──────────────────────────────────────────────── */
:root {
    --os-primary:     #2563eb;
    --os-primary-dk:  #1d4ed8;
    --os-danger:      #dc2626;
    --os-success:     #16a34a;
    --os-border:      #e2e8f0;
    --os-bg:          #f8fafc;
    --os-card:        #ffffff;
    --os-text:        #1e293b;
    --os-muted:       #64748b;
    --os-radius:      10px;
    --os-shadow:      0 1px 4px rgba(0,0,0,.08);
}

/* ── Layout ─────────────────────────────────────────────────── */
.os-page          { padding: 1.5rem; max-width: 1200px; margin: 0 auto; }
.os-header        { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.os-title         { font-size: 1.5rem; font-weight: 700; color: var(--os-text); margin: 0; }
.os-subtitle      { color: var(--os-muted); font-size: .875rem; margin: .25rem 0 0; }

/* ── Alerts ─────────────────────────────────────────────────── */
.os-alert         { padding: .875rem 1rem; border-radius: var(--os-radius); margin-bottom: 1.25rem; font-size: .875rem; }
.os-alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.os-alert-error   { background: #fef2f2; color: var(--os-danger); border: 1px solid #fecaca; }
.os-error-list    { margin: .5rem 0 0 1rem; padding: 0; }

/* ── Buttons ────────────────────────────────────────────────── */
.os-btn-primary   { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; background: var(--os-primary); color: #fff; border: none; border-radius: 8px; font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: background .15s; }
.os-btn-primary:hover   { background: var(--os-primary-dk); color: #fff; }
.os-btn-secondary { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; background: #fff; color: var(--os-text); border: 1px solid var(--os-border); border-radius: 8px; font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color .15s; }
.os-btn-secondary:hover { border-color: var(--os-primary); color: var(--os-primary); }

/* ── Icon Buttons (table actions) ───────────────────────────── */
.os-actions       { display: flex; gap: .4rem; align-items: center; }
.os-btn-icon      { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; font-size: .8rem; transition: background .15s; }
.os-btn-edit      { background: #eff6ff; color: var(--os-primary); }
.os-btn-edit:hover   { background: #dbeafe; }
.os-btn-delete    { background: #fef2f2; color: var(--os-danger); }
.os-btn-delete:hover { background: #fee2e2; }
.os-delete-form   { display: inline; }

/* ── Tabs ───────────────────────────────────────────────────── */
.os-tabs          { display: flex; gap: .25rem; border-bottom: 2px solid var(--os-border); margin-bottom: 1.5rem; }
.os-tab           { padding: .6rem 1.1rem; border: none; background: none; cursor: pointer; font-size: .875rem; color: var(--os-muted); border-bottom: 2px solid transparent; margin-bottom: -2px; border-radius: 6px 6px 0 0; transition: all .15s; }
.os-tab.active    { color: var(--os-primary); border-bottom-color: var(--os-primary); font-weight: 600; }
.os-tab-content   { display: none; }
.os-tab-content.active { display: block; }

/* ── Card ───────────────────────────────────────────────────── */
.os-card          { background: var(--os-card); border: 1px solid var(--os-border); border-radius: var(--os-radius); overflow: hidden; box-shadow: var(--os-shadow); }

/* ── Table ──────────────────────────────────────────────────── */
.os-table         { width: 100%; border-collapse: collapse; font-size: .875rem; }
.os-table th      { background: var(--os-bg); padding: .75rem 1rem; text-align: left; font-weight: 600; color: var(--os-muted); font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; border-bottom: 1px solid var(--os-border); }
.os-table td      { padding: .75rem 1rem; border-bottom: 1px solid var(--os-border); color: var(--os-text); vertical-align: middle; }
.os-table tr:last-child td { border-bottom: none; }
.os-table tr:hover td { background: #f8fafc; }
.os-name          { font-weight: 500; }
.os-muted         { color: var(--os-muted); }
.os-badge         { display: inline-block; padding: .2rem .65rem; background: #eff6ff; color: var(--os-primary); border-radius: 20px; font-size: .75rem; font-weight: 500; }
.os-avatar-sm     { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--os-border); }
.os-empty         { text-align: center; padding: 2.5rem; color: var(--os-muted); font-size: .9rem; }

/* ── Hierarchy Tree ─────────────────────────────────────────── */
.os-tree-wrapper {
    overflow-x: auto;
    padding: 1rem;
}

.os-tree {
    display: flex;
    flex-direction: column;
    gap: .75rem;
}

/* Setiap node di-indent berdasarkan depth */
.os-node {
    display: flex;
    flex-direction: column;
    gap: .5rem;
    padding-left: calc(var(--depth) * 2.5rem);
    position: relative;
}

/* Garis vertikal kiri untuk node yang punya parent */
.os-node[style*="--depth: 1"],
.os-node[style*="--depth: 2"],
.os-node[style*="--depth: 3"],
.os-node[style*="--depth: 4"] {
    border-left: 2px solid #e2e8f0;
    margin-left: calc((var(--depth) - 1) * 2.5rem + 1.25rem);
    padding-left: 1.25rem;
}

.os-node-card {
    display: inline-flex;
    align-items: center;
    gap: .75rem;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: .65rem 1rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
    min-width: 260px;
    max-width: 400px;
    transition: box-shadow .15s, border-color .15s;
}

.os-node-card:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,.1);
    border-color: #bfdbfe;
}

.os-node-photo {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
    flex-shrink: 0;
}

.os-node-info {
    flex: 1;
    min-width: 0;
}

.os-node-name {
    font-weight: 600;
    font-size: .875rem;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.os-node-position {
    font-size: .75rem;
    color: #64748b;
    margin-top: .1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.os-node-actions {
    display: flex;
    gap: .3rem;
    flex-shrink: 0;
}

.os-node-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: none;
    background: #f1f5f9;
    color: #64748b;
    cursor: pointer;
    font-size: .85rem;
    text-decoration: none;
    transition: all .15s;
    line-height: 1;
}

.os-node-btn:hover        { background: #eff6ff; color: #2563eb; }
.os-node-btn-danger:hover { background: #fef2f2; color: #dc2626; }

.os-children {
    display: flex;
    flex-direction: column;
    gap: .5rem;
    margin-top: .25rem;
}
/* ── Form Card ──────────────────────────────────────────────── */
.os-form-card     { background: var(--os-card); border: 1px solid var(--os-border); border-radius: var(--os-radius); padding: 1.75rem; box-shadow: var(--os-shadow); max-width: 760px; }
.os-form-grid     { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.os-field         { display: flex; flex-direction: column; gap: .4rem; }
.os-field-full    { grid-column: 1 / -1; }
.os-label         { font-size: .8125rem; font-weight: 600; color: var(--os-text); }
.os-required      { color: var(--os-danger); }
.os-input         { width: 100%; padding: .55rem .75rem; border: 1px solid var(--os-border); border-radius: 8px; font-size: .875rem; color: var(--os-text); background: #fff; transition: border-color .15s, box-shadow .15s; box-sizing: border-box; }
.os-input:focus   { outline: none; border-color: var(--os-primary); box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
.os-input-error   { border-color: var(--os-danger); }
.os-field-error   { font-size: .75rem; color: var(--os-danger); }
.os-hint          { font-size: .75rem; color: var(--os-muted); }

/* ── Photo Upload ───────────────────────────────────────────── */
.os-photo-preview-wrap { margin-bottom: .75rem; }
.os-photo-preview { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--os-border); display: block; margin-bottom: .4rem; }
.os-file-input    { display: none; } /* Hidden – triggered by label */
.os-file-input-wrap { display: flex; align-items: center; gap: .75rem; }
.os-file-label    { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem 1rem; background: var(--os-bg); border: 1px solid var(--os-border); border-radius: 8px; font-size: .8125rem; font-weight: 500; cursor: pointer; color: var(--os-text); transition: border-color .15s; }
.os-file-label:hover { border-color: var(--os-primary); color: var(--os-primary); }
.os-file-name     { font-size: .8125rem; color: var(--os-muted); }

/* ── Form Footer ────────────────────────────────────────────── */
.os-form-footer   { display: flex; justify-content: flex-end; gap: .75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--os-border); }

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 640px) {
    .os-form-grid { grid-template-columns: 1fr; }
    .os-field-full { grid-column: auto; }
    .os-table { font-size: .8rem; }
    .os-table th, .os-table td { padding: .6rem .65rem; }
    .os-node-card { min-width: 200px; }
    .os-form-card { padding: 1.25rem; }
}
</style>