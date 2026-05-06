<style>
/* ════════════════════════════════════════════════════════════════
   BMN Asset Module — Scoped CSS (prefix: bmn-)
════════════════════════════════════════════════════════════════ */
:root {
    --bmn-primary:   #1e40af;
    --bmn-primary-h: #1d4ed8;
    --bmn-danger:    #dc2626;
    --bmn-success:   #16a34a;
    --bmn-warning:   #d97706;
    --bmn-border:    #e2e8f0;
    --bmn-bg:        #f8fafc;
    --bmn-card:      #ffffff;
    --bmn-text:      #1e293b;
    --bmn-muted:     #64748b;
    --bmn-radius:    10px;
    --bmn-shadow:    0 1px 4px rgba(0,0,0,.07);
}

/* ── Layout ─────────────────────────────────────────────────── */
.bmn-page      { padding: 1.5rem; max-width: 1280px; margin: 0 auto; }
.bmn-header    { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.bmn-title     { font-size: 1.5rem; font-weight: 700; color: var(--bmn-text); margin: 0; }
.bmn-subtitle  { color: var(--bmn-muted); font-size: .875rem; margin: .25rem 0 0; }

/* ── Summary Cards ──────────────────────────────────────────── */
.bmn-summary-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
.bmn-summary-card { background: var(--bmn-card); border: 1px solid var(--bmn-border); border-radius: var(--bmn-radius); padding: 1rem 1.25rem; display: flex; align-items: center; gap: .875rem; box-shadow: var(--bmn-shadow); }
.bmn-summary-card-wide { grid-column: span 1; }
.bmn-summary-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.bmn-summary-val  { font-size: 1.3rem; font-weight: 700; color: var(--bmn-text); line-height: 1; }
.bmn-summary-label { font-size: .75rem; color: var(--bmn-muted); margin-top: .25rem; }

/* ── Filter Bar ─────────────────────────────────────────────── */
.bmn-filter-bar  { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
.bmn-filter-form { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; }
.bmn-search      { min-width: 220px; }
.bmn-select      { min-width: 150px; }
.bmn-export-group { display: flex; gap: .4rem; }
.bmn-btn-export  { padding: .5rem .9rem; background: #fff; border: 1px solid var(--bmn-border); border-radius: 8px; font-size: .8125rem; color: var(--bmn-text); text-decoration: none; font-weight: 500; transition: border-color .15s; }
.bmn-btn-export:hover { border-color: var(--bmn-primary); color: var(--bmn-primary); }

/* ── Alerts ─────────────────────────────────────────────────── */
.bmn-alert         { padding: .875rem 1rem; border-radius: var(--bmn-radius); margin-bottom: 1.25rem; font-size: .875rem; }
.bmn-alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.bmn-alert-error   { background: #fef2f2; color: var(--bmn-danger); border: 1px solid #fecaca; }

/* ── Buttons ────────────────────────────────────────────────── */
.bmn-btn-primary   { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; background: var(--bmn-primary); color: #fff; border: none; border-radius: 8px; font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: background .15s; }
.bmn-btn-primary:hover   { background: var(--bmn-primary-h); color: #fff; }
.bmn-btn-secondary { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; background: #fff; color: var(--bmn-text); border: 1px solid var(--bmn-border); border-radius: 8px; font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color .15s; }
.bmn-btn-secondary:hover { border-color: var(--bmn-primary); color: var(--bmn-primary); }

/* ── Icon Buttons ───────────────────────────────────────────── */
.bmn-actions   { display: flex; gap: .35rem; align-items: center; }
.bmn-btn-icon  { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; transition: background .15s; }
.bmn-btn-view:hover   { background: #eff6ff; color: var(--bmn-primary); }
.bmn-btn-view         { background: #f1f5f9; color: var(--bmn-muted); }
.bmn-btn-edit         { background: #f1f5f9; color: var(--bmn-muted); }
.bmn-btn-edit:hover   { background: #eff6ff; color: var(--bmn-primary); }
.bmn-btn-delete       { background: #f1f5f9; color: var(--bmn-muted); }
.bmn-btn-delete:hover { background: #fef2f2; color: var(--bmn-danger); }

/* ── Card ───────────────────────────────────────────────────── */
.bmn-card         { background: var(--bmn-card); border: 1px solid var(--bmn-border); border-radius: var(--bmn-radius); box-shadow: var(--bmn-shadow); overflow: hidden; }
.bmn-card-header  { padding: .875rem 1.25rem; font-weight: 600; font-size: .9rem; border-bottom: 1px solid var(--bmn-border); color: var(--bmn-text); background: var(--bmn-bg); }
.bmn-card-body    { padding: 1.25rem; }

/* ── Table ──────────────────────────────────────────────────── */
.bmn-table     { width: 100%; border-collapse: collapse; font-size: .875rem; }
.bmn-table th  { background: var(--bmn-bg); padding: .75rem 1rem; text-align: left; font-weight: 600; color: var(--bmn-muted); font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; border-bottom: 1px solid var(--bmn-border); }
.bmn-table td  { padding: .75rem 1rem; border-bottom: 1px solid var(--bmn-border); vertical-align: middle; }
.bmn-table tr:last-child td { border-bottom: none; }
.bmn-table tr:hover td { background: #f8fafc; }
.bmn-pagination { padding: 1rem 1.25rem; border-top: 1px solid var(--bmn-border); }

/* ── Badges & Tags ──────────────────────────────────────────── */
.bmn-badge          { display: inline-block; padding: .2rem .7rem; border-radius: 20px; font-size: .75rem; font-weight: 600; }
.bmn-badge-success  { background: #dcfce7; color: #15803d; }
.bmn-badge-warning  { background: #fef3c7; color: #b45309; }
.bmn-badge-danger   { background: #fee2e2; color: #dc2626; }
.bmn-tag            { display: inline-block; padding: .2rem .65rem; background: #eff6ff; color: var(--bmn-primary); border-radius: 20px; font-size: .75rem; font-weight: 500; white-space: nowrap; }

/* ── Misc ───────────────────────────────────────────────────── */
.bmn-avatar      { width: 38px; height: 38px; border-radius: 8px; object-fit: cover; border: 1px solid var(--bmn-border); }
.bmn-mono        { font-family: 'Courier New', monospace; font-size: .8125rem; }
.bmn-muted       { color: var(--bmn-muted); font-size: .85rem; }
.bmn-empty       { text-align: center; color: var(--bmn-muted); padding: 2.5rem; font-size: .9rem; }
.bmn-asset-name  { font-weight: 500; color: var(--bmn-text); }
.bmn-asset-sub   { font-size: .78rem; color: var(--bmn-muted); margin-top: .1rem; }
.bmn-link        { color: var(--bmn-primary); text-decoration: none; font-size: .875rem; }
.bmn-link:hover  { text-decoration: underline; }

/* ── Detail Page ────────────────────────────────────────────── */
.bmn-detail-grid     { display: grid; grid-template-columns: 340px 1fr; gap: 1.25rem; align-items: start; }
.bmn-detail-photo    { width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 2px solid var(--bmn-border); }
.bmn-detail-rows     { display: flex; flex-direction: column; gap: .6rem; }
.bmn-detail-row      { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; font-size: .875rem; padding-bottom: .6rem; border-bottom: 1px solid var(--bmn-border); }
.bmn-detail-row:last-child { border-bottom: none; padding-bottom: 0; }
.bmn-detail-row-col  { flex-direction: column; gap: .25rem; }
.bmn-detail-label    { color: var(--bmn-muted); font-size: .8125rem; flex-shrink: 0; }

/* ── Mutasi Timeline ────────────────────────────────────────── */
.bmn-mutation-row    { display: flex; gap: .875rem; padding: .875rem 1.25rem; border-bottom: 1px solid var(--bmn-border); }
.bmn-mutation-row:last-child { border-bottom: none; }
.bmn-mutation-dot    { width: 10px; height: 10px; border-radius: 50%; background: var(--bmn-primary); margin-top: .35rem; flex-shrink: 0; }
.bmn-mutation-title  { font-size: .875rem; font-weight: 500; color: var(--bmn-text); display: flex; align-items: center; flex-wrap: wrap; gap: .2rem; }
.bmn-mutation-meta   { font-size: .78rem; color: var(--bmn-muted); margin-top: .2rem; }

/* ── Form ───────────────────────────────────────────────────── */
.bmn-section-title  { font-weight: 700; font-size: .8rem; text-transform: uppercase; letter-spacing: .06em; color: var(--bmn-muted); margin-bottom: .875rem; padding-bottom: .5rem; border-bottom: 1px solid var(--bmn-border); }
.bmn-form-grid      { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.bmn-form-grid-2    { display: grid; grid-template-columns: 1fr 1fr; gap: .875rem; }
.bmn-field          { display: flex; flex-direction: column; gap: .375rem; }
.bmn-field-full     { grid-column: 1 / -1; }
.bmn-label          { font-size: .8125rem; font-weight: 600; color: var(--bmn-text); }
.bmn-req            { color: var(--bmn-danger); }
.bmn-input          { width: 100%; padding: .55rem .75rem; border: 1px solid var(--bmn-border); border-radius: 8px; font-size: .875rem; color: var(--bmn-text); background: #fff; transition: border-color .15s, box-shadow .15s; box-sizing: border-box; }
.bmn-input:focus    { outline: none; border-color: var(--bmn-primary); box-shadow: 0 0 0 3px rgba(30,64,175,.1); }
.bmn-input-error    { border-color: var(--bmn-danger); }
.bmn-textarea       { resize: vertical; min-height: 80px; }
.bmn-field-error    { font-size: .75rem; color: var(--bmn-danger); }
.bmn-hint           { font-size: .75rem; color: var(--bmn-muted); }
.bmn-form-footer    { display: flex; justify-content: flex-end; gap: .75rem; margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid var(--bmn-border); }

/* ── File Input ─────────────────────────────────────────────── */
.bmn-file-input   { display: none; }
.bmn-file-wrap    { display: flex; align-items: center; gap: .6rem; }
.bmn-file-label   { display: inline-flex; align-items: center; gap: .4rem; padding: .45rem .9rem; background: var(--bmn-bg); border: 1px solid var(--bmn-border); border-radius: 8px; font-size: .8125rem; font-weight: 500; cursor: pointer; color: var(--bmn-text); transition: border-color .15s; white-space: nowrap; }
.bmn-file-label:hover { border-color: var(--bmn-primary); color: var(--bmn-primary); }
.bmn-file-name    { font-size: .8rem; color: var(--bmn-muted); }
.bmn-preview-img  { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 2px solid var(--bmn-border); margin-bottom: .5rem; display: block; }

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 1024px) {
    .bmn-summary-grid { grid-template-columns: repeat(3, 1fr); }
    .bmn-detail-grid  { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .bmn-summary-grid { grid-template-columns: 1fr 1fr; }
    .bmn-form-grid, .bmn-form-grid-2 { grid-template-columns: 1fr; }
    .bmn-field-full   { grid-column: auto; }
    .bmn-filter-form  { width: 100%; }
    .bmn-search       { width: 100%; }
}
</style>