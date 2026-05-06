<style>
/* ════════════════════════════════════════════════════════════════
   Kanban Module — Scoped CSS (prefix: kb-)
════════════════════════════════════════════════════════════════ */
:root {
    --kb-bg:      #f1f5f9;
    --kb-card:    #ffffff;
    --kb-border:  #e2e8f0;
    --kb-text:    #1e293b;
    --kb-muted:   #64748b;
    --kb-radius:  10px;
    --kb-shadow:  0 1px 3px rgba(0,0,0,.08);
}

/* ── Layout ─────────────────────────────────────────────────── */
.kb-page    { padding: 1.5rem; }
.kb-header  { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem; }
.kb-title   { font-size: 1.4rem; font-weight: 700; color: var(--kb-text); margin: 0; }
.kb-subtitle { color: var(--kb-muted); font-size: .875rem; margin: .2rem 0 0; }

/* ── Summary ────────────────────────────────────────────────── */
.kb-summary        { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.kb-summary-item   { background: var(--kb-card); border: 1px solid var(--kb-border); border-radius: var(--kb-radius); padding: .875rem 1.25rem; display: flex; flex-direction: column; gap: .2rem; min-width: 130px; box-shadow: var(--kb-shadow); }
.kb-summary-val    { font-size: 1.5rem; font-weight: 700; color: var(--kb-text); }
.kb-summary-label  { font-size: .75rem; color: var(--kb-muted); }
.kb-summary-danger .kb-summary-val { color: #dc2626; }
.kb-summary-orange .kb-summary-val { color: #d97706; }
.kb-summary-green  .kb-summary-val { color: #16a34a; }

/* ── Alert ──────────────────────────────────────────────────── */
.kb-alert         { padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .875rem; }
.kb-alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

/* ── Board ──────────────────────────────────────────────────── */
.kb-board {
    display: grid;
    grid-template-columns: repeat(5, minmax(240px, 1fr));
    gap: 1rem;
    align-items: start;
    overflow-x: auto;
    padding-bottom: 1rem;
}

/* ── Column ─────────────────────────────────────────────────── */
.kb-column       { background: #f8fafc; border: 1px solid var(--kb-border); border-radius: var(--kb-radius); min-height: 400px; display: flex; flex-direction: column; }
.kb-col-header   { display: flex; justify-content: space-between; align-items: center; padding: .75rem 1rem; border-radius: var(--kb-radius) var(--kb-radius) 0 0; border-bottom: 2px solid; }
.kb-col-title    { font-weight: 700; font-size: .875rem; }
.kb-col-count    { font-size: .75rem; font-weight: 700; padding: .15rem .5rem; border-radius: 20px; background: rgba(255,255,255,.5); }

/* Column color themes */
.kb-col-slate  { background: #f1f5f9; border-color: #94a3b8; color: #475569; }
.kb-col-blue   { background: #eff6ff; border-color: #3b82f6; color: #1d4ed8; }
.kb-col-orange { background: #fff7ed; border-color: #f97316; color: #c2410c; }
.kb-col-green  { background: #f0fdf4; border-color: #22c55e; color: #15803d; }
.kb-col-purple { background: #faf5ff; border-color: #a855f7; color: #7c3aed; }

/* ── Cards ──────────────────────────────────────────────────── */
.kb-cards      { padding: .75rem; display: flex; flex-direction: column; gap: .625rem; flex: 1; }
.kb-empty-col  { text-align: center; color: var(--kb-muted); font-size: .8rem; padding: 2rem 1rem; }

.kb-card {
    background: var(--kb-card);
    border: 1px solid var(--kb-border);
    border-radius: 8px;
    padding: .875rem;
    box-shadow: var(--kb-shadow);
    transition: box-shadow .15s, transform .15s;
    cursor: default;
}
.kb-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); transform: translateY(-1px); }
.kb-card-overdue { border-left: 3px solid #dc2626; }

.kb-card-top    { margin-bottom: .5rem; }
.kb-card-title  { font-weight: 600; font-size: .875rem; color: var(--kb-text); line-height: 1.4; }
.kb-card-num    { font-size: .75rem; color: var(--kb-muted); font-family: monospace; margin-top: .15rem; }

.kb-card-revisi {
    font-size: .75rem; color: #c2410c; background: #fff7ed;
    border: 1px solid #fed7aa; border-radius: 6px;
    padding: .35rem .6rem; margin-bottom: .5rem;
    display: flex; align-items: flex-start; gap: .3rem;
}
.kb-revisi-icon { flex-shrink: 0; }

.kb-card-meta     { display: flex; justify-content: space-between; align-items: center; gap: .5rem; margin-top: .5rem; flex-wrap: wrap; }
.kb-card-assignee { display: flex; align-items: center; gap: .4rem; font-size: .78rem; color: var(--kb-muted); }
.kb-avatar        { width: 22px; height: 22px; border-radius: 50%; background: #3b82f6; color: #fff; font-size: .65rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.kb-card-deadline { display: flex; align-items: center; gap: .25rem; font-size: .75rem; color: var(--kb-muted); }
.kb-overdue-text  { color: #dc2626 !important; }
.kb-overdue-badge { background: #fee2e2; color: #dc2626; font-size: .65rem; font-weight: 700; padding: .1rem .35rem; border-radius: 4px; }

.kb-card-footer { margin-top: .75rem; padding-top: .625rem; border-top: 1px solid var(--kb-border); }
.kb-btn-move    { width: 100%; padding: .4rem; background: #f8fafc; border: 1px solid var(--kb-border); border-radius: 6px; font-size: .78rem; color: #3b82f6; font-weight: 500; cursor: pointer; transition: all .15s; }
.kb-btn-move:hover { background: #eff6ff; border-color: #3b82f6; }

/* ── Buttons ────────────────────────────────────────────────── */
.kb-btn-primary   { padding: .55rem 1.1rem; background: #1e40af; color: #fff; border: none; border-radius: 8px; font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: .4rem; transition: background .15s; }
.kb-btn-primary:hover   { background: #1d4ed8; color: #fff; }
.kb-btn-secondary { padding: .55rem 1.1rem; background: #fff; color: var(--kb-text); border: 1px solid var(--kb-border); border-radius: 8px; font-size: .875rem; font-weight: 500; cursor: pointer; transition: border-color .15s; }
.kb-btn-secondary:hover { border-color: #1e40af; color: #1e40af; }

/* ── Modal ──────────────────────────────────────────────────── */
.kb-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.kb-modal         { background: #fff; border-radius: 12px; width: 100%; max-width: 440px; box-shadow: 0 20px 50px rgba(0,0,0,.2); }
.kb-modal-header  { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid var(--kb-border); font-weight: 700; font-size: .95rem; }
.kb-modal-close   { background: none; border: none; font-size: 1rem; cursor: pointer; color: var(--kb-muted); padding: .25rem; }
.kb-modal-body    { padding: 1.25rem; }
.kb-modal-footer  { display: flex; justify-content: flex-end; gap: .6rem; padding: 1rem 1.25rem; border-top: 1px solid var(--kb-border); }

/* ── Form Elements ──────────────────────────────────────────── */
.kb-field    { display: flex; flex-direction: column; gap: .4rem; margin-bottom: 1rem; }
.kb-label    { font-size: .8125rem; font-weight: 600; color: var(--kb-text); }
.kb-req      { color: #dc2626; }
.kb-input    { width: 100%; padding: .55rem .75rem; border: 1px solid var(--kb-border); border-radius: 8px; font-size: .875rem; color: var(--kb-text); box-sizing: border-box; }
.kb-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
.kb-textarea { resize: vertical; min-height: 80px; }
.kb-muted    { color: var(--kb-muted); font-size: .875rem; }

/* ── Status Option Pills (modal) ────────────────────────────── */
.kb-status-options { display: flex; gap: .5rem; flex-wrap: wrap; }
.kb-status-option  { padding: .5rem 1rem; border: 2px solid var(--kb-border); border-radius: 20px; font-size: .8125rem; font-weight: 600; cursor: pointer; transition: all .15s; background: #fff; color: var(--kb-text); }
.kb-status-option:hover    { border-color: #3b82f6; color: #1d4ed8; }
.kb-status-option.selected { border-color: #1e40af; background: #1e40af; color: #fff; }

/* Status color per pill */
.kb-pill-disetujui.selected { background: #16a34a; border-color: #16a34a; }
.kb-pill-revisi.selected    { background: #d97706; border-color: #d97706; }
.kb-pill-selesai.selected   { background: #7c3aed; border-color: #7c3aed; }
.kb-pill-diajukan.selected  { background: #2563eb; border-color: #2563eb; }
.kb-pill-draft.selected     { background: #64748b; border-color: #64748b; }

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 900px) {
    .kb-board { grid-template-columns: repeat(3, minmax(220px, 1fr)); }
}
@media (max-width: 600px) {
    .kb-board { grid-template-columns: repeat(2, minmax(200px, 1fr)); }
}
</style>