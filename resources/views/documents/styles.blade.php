<style>
/* ════════════════════════════════════════════════════════════════
   Documents Module — Scoped CSS (prefix: doc-)
════════════════════════════════════════════════════════════════ */
:root {
    --doc-primary:  #1e40af;
    --doc-danger:   #dc2626;
    --doc-border:   #e2e8f0;
    --doc-bg:       #f8fafc;
    --doc-card:     #ffffff;
    --doc-text:     #1e293b;
    --doc-muted:    #64748b;
    --doc-radius:   10px;
    --doc-shadow:   0 1px 4px rgba(0,0,0,.07);
}

/* ── Layout ─────────────────────────────────────────────────── */
.doc-page       { padding: 1.5rem; max-width: 1200px; margin: 0 auto; }
.doc-header     { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; gap: 1rem; flex-wrap: wrap; }
.doc-title      { font-size: 1.4rem; font-weight: 700; color: var(--doc-text); margin: 0; }
.doc-subtitle   { color: var(--doc-muted); font-size: .875rem; margin: .2rem 0 0; }
.doc-mono       { font-family: 'Courier New', monospace; font-size: .8125rem; }

/* ── Alert ──────────────────────────────────────────────────── */
.doc-alert         { padding: .875rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: .875rem; }
.doc-alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.doc-alert-error   { background: #fef2f2; color: var(--doc-danger); border: 1px solid #fecaca; }

/* ── Summary Grid ───────────────────────────────────────────── */
.doc-summary-grid { display: flex; gap: .75rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
.doc-summary-card { padding: .75rem 1.1rem; border-radius: 8px; border: 1px solid var(--doc-border); min-width: 110px; }
.doc-summary-val  { font-size: 1.4rem; font-weight: 700; }
.doc-summary-label { font-size: .72rem; color: var(--doc-muted); margin-top: .1rem; }

.doc-summary-slate  { background: #f8fafc; }
.doc-summary-slate .doc-summary-val  { color: #475569; }
.doc-summary-blue   { background: #eff6ff; }
.doc-summary-blue .doc-summary-val   { color: #1d4ed8; }
.doc-summary-orange { background: #fff7ed; }
.doc-summary-orange .doc-summary-val { color: #c2410c; }
.doc-summary-green  { background: #f0fdf4; }
.doc-summary-green .doc-summary-val  { color: #15803d; }
.doc-summary-purple { background: #faf5ff; }
.doc-summary-purple .doc-summary-val { color: #7c3aed; }

/* ── Filter Bar ─────────────────────────────────────────────── */
.doc-filter-bar { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; margin-bottom: 1rem; }
.doc-search     { min-width: 240px; flex: 1; max-width: 360px; }
.doc-select     { min-width: 160px; }

/* ── Buttons ────────────────────────────────────────────────── */
.doc-btn-primary   { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; background: var(--doc-primary); color: #fff; border: none; border-radius: 8px; font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: background .15s; }
.doc-btn-primary:hover   { background: #1d4ed8; color: #fff; }
.doc-btn-secondary { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; background: #fff; color: var(--doc-text); border: 1px solid var(--doc-border); border-radius: 8px; font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color .15s; }
.doc-btn-secondary:hover { border-color: var(--doc-primary); color: var(--doc-primary); }

.doc-btn-status { padding: .5rem 1rem; border: 2px solid transparent; border-radius: 8px; font-size: .8125rem; font-weight: 600; cursor: pointer; transition: opacity .15s, transform .1s; }
.doc-btn-status:hover { transform: translateY(-1px); }

/* ── Icon Buttons ───────────────────────────────────────────── */
.doc-actions   { display: flex; gap: .35rem; }
.doc-btn-icon  { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; transition: background .15s; }
.doc-btn-view  { background: #f1f5f9; color: var(--doc-muted); }
.doc-btn-view:hover   { background: #eff6ff; color: var(--doc-primary); }
.doc-btn-edit  { background: #f1f5f9; color: var(--doc-muted); }
.doc-btn-edit:hover   { background: #eff6ff; color: var(--doc-primary); }
.doc-btn-delete { background: #f1f5f9; color: var(--doc-muted); }
.doc-btn-delete:hover { background: #fef2f2; color: var(--doc-danger); }

/* ── Card ───────────────────────────────────────────────────── */
.doc-card         { background: var(--doc-card); border: 1px solid var(--doc-border); border-radius: var(--doc-radius); box-shadow: var(--doc-shadow); overflow: hidden; }
.doc-card-header  { padding: .875rem 1.25rem; font-weight: 600; font-size: .9rem; border-bottom: 1px solid var(--doc-border); color: var(--doc-text); background: var(--doc-bg); }
.doc-card-body    { padding: 1.25rem; }
.doc-card-revisi  { border-color: #fed7aa; }

/* ── Table ──────────────────────────────────────────────────── */
.doc-table     { width: 100%; border-collapse: collapse; font-size: .875rem; }
.doc-table th  { background: var(--doc-bg); padding: .75rem 1rem; text-align: left; font-weight: 600; color: var(--doc-muted); font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; border-bottom: 1px solid var(--doc-border); white-space: nowrap; }
.doc-table td  { padding: .75rem 1rem; border-bottom: 1px solid var(--doc-border); vertical-align: middle; }
.doc-table tr:last-child td { border-bottom: none; }
.doc-table tr:hover td { background: #f8fafc; }
.doc-pagination { padding: 1rem 1.25rem; border-top: 1px solid var(--doc-border); }
.doc-empty      { text-align: center; color: var(--doc-muted); padding: 2.5rem; font-size: .9rem; }

/* ── Badges ─────────────────────────────────────────────────── */
.doc-badge        { display: inline-block; padding: .2rem .7rem; border-radius: 20px; font-size: .75rem; font-weight: 600; white-space: nowrap; }
.doc-badge-lg     { font-size: .825rem; padding: .3rem .9rem; }
.doc-badge-slate  { background: #f1f5f9; color: #475569; }
.doc-badge-blue   { background: #dbeafe; color: #1d4ed8; }
.doc-badge-orange { background: #ffedd5; color: #c2410c; }
.doc-badge-green  { background: #dcfce7; color: #15803d; }
.doc-badge-purple { background: #f3e8ff; color: #7c3aed; }

/* ── Misc ───────────────────────────────────────────────────── */
.doc-link-title    { font-weight: 500; color: var(--doc-primary); text-decoration: none; }
.doc-link-title:hover { text-decoration: underline; }
.doc-revisi-hint   { font-size: .75rem; color: #c2410c; margin-top: .2rem; }
.doc-overdue       { color: #dc2626; font-weight: 500; }
.doc-overdue-badge { background: #fee2e2; color: #dc2626; font-size: .68rem; font-weight: 700; padding: .1rem .4rem; border-radius: 4px; margin-left: .25rem; }
.doc-muted         { color: var(--doc-muted); font-size: .875rem; }
.doc-assignee      { display: flex; align-items: center; gap: .45rem; font-size: .875rem; }
.doc-avatar        { width: 26px; height: 26px; border-radius: 50%; background: #3b82f6; color: #fff; font-size: .68rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

/* ── Form ───────────────────────────────────────────────────── */
.doc-form-wrap  { max-width: 760px; }
.doc-card-body  { padding: 1.5rem; }
.doc-form-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.doc-field      { display: flex; flex-direction: column; gap: .375rem; }
.doc-field-full { grid-column: 1 / -1; }
.doc-label      { font-size: .8125rem; font-weight: 600; color: var(--doc-text); }
.doc-req        { color: var(--doc-danger); }
.doc-input      { width: 100%; padding: .55rem .75rem; border: 1px solid var(--doc-border); border-radius: 8px; font-size: .875rem; color: var(--doc-text); background: #fff; transition: border-color .15s, box-shadow .15s; box-sizing: border-box; }
.doc-input:focus    { outline: none; border-color: var(--doc-primary); box-shadow: 0 0 0 3px rgba(30,64,175,.1); }
.doc-input-error    { border-color: var(--doc-danger) !important; }
.doc-textarea       { resize: vertical; min-height: 100px; }
.doc-field-error    { font-size: .75rem; color: var(--doc-danger); }
.doc-hint           { font-size: .75rem; color: var(--doc-muted); }
.doc-form-footer    { display: flex; justify-content: flex-end; gap: .75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--doc-border); }
.doc-revisi-box     { display: flex; gap: .5rem; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: .75rem 1rem; color: #92400e; font-size: .875rem; line-height: 1.5; }

/* ── Show Page Layout ───────────────────────────────────────── */
.doc-show-grid { display: grid; grid-template-columns: 380px 1fr; gap: 1rem; align-items: start; }

/* ── Detail Rows ────────────────────────────────────────────── */
.doc-detail-rows  { display: flex; flex-direction: column; }
.doc-detail-row   { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; padding: .65rem 0; border-bottom: 1px solid var(--doc-border); font-size: .875rem; }
.doc-detail-row:last-child  { border-bottom: none; }
.doc-detail-row-col { flex-direction: column; gap: .25rem; }
.doc-detail-label { color: var(--doc-muted); font-size: .8125rem; flex-shrink: 0; }

/* ── Audit Trail History ────────────────────────────────────── */
.doc-history-item { display: flex; gap: .875rem; padding: .875rem 1.25rem; border-bottom: 1px solid var(--doc-border); }
.doc-history-item:last-child { border-bottom: none; }

.doc-history-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; margin-top: .35rem; }
.doc-dot-slate    { background: #94a3b8; }
.doc-dot-blue     { background: #3b82f6; }
.doc-dot-orange   { background: #f97316; }
.doc-dot-green    { background: #22c55e; }
.doc-dot-purple   { background: #a855f7; }

.doc-history-body  { flex: 1; min-width: 0; }
.doc-history-title { font-size: .875rem; color: var(--doc-text); display: flex; align-items: center; flex-wrap: wrap; gap: .1rem; }
.doc-history-meta  { font-size: .775rem; color: var(--doc-muted); margin-top: .2rem; display: flex; gap: .35rem; align-items: center; flex-wrap: wrap; }
.doc-dot-sep       { color: #cbd5e1; }
.doc-history-note  { margin-top: .35rem; font-size: .8125rem; color: #475569; background: var(--doc-bg); border-radius: 6px; padding: .4rem .65rem; border-left: 3px solid var(--doc-border); }

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 900px) {
    .doc-show-grid  { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .doc-form-grid  { grid-template-columns: 1fr; }
    .doc-field-full { grid-column: auto; }
    .doc-table      { font-size: .8rem; }
    .doc-search     { max-width: 100%; }
}
</style>