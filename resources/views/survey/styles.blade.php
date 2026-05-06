<style>
/* ════════════════════════════════════════════════════════════════
   Survey Module — Scoped CSS (prefix: sv-)
════════════════════════════════════════════════════════════════ */
.sv-page     { padding: 1.5rem; max-width: 1200px; margin: 0 auto; }
.sv-header   { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; gap: 1rem; flex-wrap: wrap; }
.sv-title    { font-size: 1.4rem; font-weight: 700; color: var(--text-1); margin: 0; }
.sv-subtitle { color: var(--text-3); font-size: .875rem; margin: .2rem 0 0; }

/* Buttons */
.sv-btn-primary   { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; background: var(--blue); color: #fff; border: none; border-radius: var(--radius-sm); font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: background .15s; }
.sv-btn-primary:hover   { background: #1347C8; color: #fff; }
.sv-btn-secondary { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; background: #fff; color: var(--text-2); border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color .15s; }
.sv-btn-secondary:hover { border-color: var(--blue); color: var(--blue); }
.sv-btn-sm   { padding: .4rem .8rem; font-size: .8rem; }

/* Icon buttons */
.sv-actions  { display: flex; gap: .35rem; }
.sv-btn-icon { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 6px; border: none; background: var(--surface-2); color: var(--text-2); cursor: pointer; font-size: .85rem; transition: background .15s; }
.sv-btn-view:hover   { background: var(--blue-light); }
.sv-btn-edit:hover   { background: var(--blue-light); }
.sv-btn-result:hover { background: var(--green-light); }
.sv-btn-delete:hover { background: var(--red-light); }

/* Card */
.sv-card        { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
.sv-card-header { padding: .875rem 1.25rem; font-weight: 600; font-size: .9rem; background: var(--bg); border-bottom: 1px solid var(--border); color: var(--text-1); }
.sv-card-body   { padding: 1.25rem; display: flex; flex-direction: column; gap: .875rem; }

/* Table */
.sv-table    { width: 100%; border-collapse: collapse; font-size: .875rem; }
.sv-table th { padding: .7rem 1rem; text-align: left; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-3); border-bottom: 1px solid var(--border); background: var(--bg); }
.sv-table td { padding: .7rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
.sv-table tr:last-child td { border-bottom: none; }
.sv-table tr:hover td { background: #f9fafb; }
.sv-name     { font-weight: 500; color: var(--text-1); }
.sv-sub      { font-size: .75rem; color: var(--text-3); margin-top: .15rem; }
.sv-muted    { color: var(--text-3); font-size: .855rem; }
.sv-mono     { font-family: 'Courier New', monospace; font-size: .8rem; }
.sv-empty    { text-align: center; padding: 2.5rem; color: var(--text-3); font-size: .9rem; }

/* Badges */
.sv-badge        { display: inline-block; padding: .2rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 600; }
.sv-badge-green  { background: var(--green-light); color: var(--green); }
.sv-badge-red    { background: var(--red-light); color: var(--red); }
.sv-badge-slate  { background: var(--surface-2); color: var(--text-2); }

/* Form fields */
.sv-field      { display: flex; flex-direction: column; gap: .375rem; }
.sv-label      { font-size: .8125rem; font-weight: 600; color: var(--text-1); }
.sv-req        { color: var(--red); }
.sv-hint       { font-size: .73rem; color: var(--text-3); }
.sv-input      { width: 100%; padding: .55rem .75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: .875rem; color: var(--text-1); background: #fff; transition: border-color .15s, box-shadow .15s; box-sizing: border-box; font-family: inherit; }
.sv-input:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(28,100,242,.1); }
.sv-input-error { border-color: var(--red) !important; }
.sv-textarea    { resize: vertical; min-height: 80px; }
.sv-field-error { font-size: .75rem; color: var(--red); }
.sv-link        { color: var(--blue); text-decoration: none; }
.sv-link:hover  { text-decoration: underline; }

/* Form builder layout */
.sv-form-layout { display: grid; grid-template-columns: 320px 1fr; gap: 1.25rem; align-items: start; }

/* Question block */
.sv-qblock        { border-bottom: 1px solid var(--border); }
.sv-qblock:last-child { border-bottom: none; }
.sv-qblock-header { display: flex; align-items: center; gap: .75rem; padding: .75rem 1.25rem; background: var(--bg); border-bottom: 1px solid var(--border); }
.sv-qblock-num    { width: 24px; height: 24px; border-radius: 50%; background: var(--blue); color: #fff; font-size: .72rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.sv-qblock-type-label { flex: 1; font-size: .8rem; font-weight: 500; color: var(--text-2); }
.sv-qblock-remove { background: none; border: none; color: var(--text-3); cursor: pointer; font-size: .9rem; padding: .25rem; border-radius: 4px; transition: background .15s, color .15s; }
.sv-qblock-remove:hover { background: var(--red-light); color: var(--red); }
.sv-qblock-body   { padding: 1rem 1.25rem; display: flex; flex-direction: column; gap: .75rem; }

/* Rating preview */
.sv-rating-preview { display: flex; align-items: center; }
.sv-star           { font-size: 1.4rem; color: #f59e0b; line-height: 1; }

/* Show page */
.sv-show-grid    { display: grid; grid-template-columns: 300px 1fr; gap: 1.25rem; align-items: start; }
.sv-detail-rows  { display: flex; flex-direction: column; }
.sv-detail-row   { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; padding: .6rem 0; border-bottom: 1px solid var(--border); font-size: .875rem; }
.sv-detail-row:last-child { border-bottom: none; }
.sv-detail-label { color: var(--text-3); font-size: .8rem; flex-shrink: 0; }
.sv-qnum         { width: 22px; height: 22px; border-radius: 50%; background: var(--blue-light); color: var(--blue); font-size: .7rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: .1rem; }

/* Responsive */
@media (max-width: 900px) {
    .sv-form-layout { grid-template-columns: 1fr; }
    .sv-show-grid   { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .sv-page { padding: 1rem; }
    .sv-table th, .sv-table td { padding: .6rem .75rem; }
}
</style>