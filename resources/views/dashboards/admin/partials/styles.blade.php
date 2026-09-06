<style>
    .admin-shell { display: grid; gap: 22px; }
    .hero { display: grid; grid-template-columns: 1.35fr .65fr; gap: 18px; align-items: stretch; }
    .hero-main { border: 1px solid rgba(255, 255, 255, .16); border-radius: 8px; background: linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(226, 232, 240, .92)); padding: 24px; }
    .hero-side { border: 1px solid rgba(255, 255, 255, .16); border-radius: 8px; background: linear-gradient(135deg, #4b5563 0%, #111827 58%, #020617 100%); color: #fff; padding: 24px; }
    .hero-side p, .hero-side .muted { color: #dbe4f0; }
    .eyebrow { color: #0f766e; font-size: 12px; font-weight: 800; letter-spacing: 0; text-transform: uppercase; }
    .grid { display: grid; gap: 16px; }
    .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .card { border: 1px solid rgba(203, 213, 225, .72); border-radius: 8px; background: rgba(255, 255, 255, .96); padding: 18px; box-shadow: 0 16px 36px rgba(0, 0, 0, .13); }
    .metric { min-height: 112px; display: flex; flex-direction: column; justify-content: space-between; }
    .metric strong { font-size: 28px; line-height: 1; }
    .muted { color: #667085; font-size: 13px; }
    .page-links { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .page-link { display: grid; gap: 8px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #14213d; padding: 16px; text-decoration: none; }
    .page-link strong { font-size: 16px; }
    .section-title { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 12px; }
    .section-title h2 { margin: 0; font-size: 22px; }
    .section-actions { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; min-width: 860px; border-collapse: collapse; }
    th, td { border-bottom: 1px solid #e5eaf2; padding: 11px 10px; text-align: left; vertical-align: top; font-size: 13px; }
    th { color: #667085; font-size: 12px; text-transform: uppercase; }
    .form-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
    label { display: grid; gap: 5px; color: #667085; font-size: 12px; font-weight: 800; }
    input, select, textarea { width: 100%; border: 1px solid #cbd5e1; border-radius: 7px; padding: 9px 10px; color: #14213d; background: #fff; font: inherit; }
    textarea { min-height: 74px; resize: vertical; }
    .span-2 { grid-column: span 2; }
    .span-4 { grid-column: span 4; }
    .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .inline-form { display: flex; gap: 7px; align-items: center; }
    .inline-form select { min-width: 132px; }
    .secondary { border: 1px solid #cbd5e1; background: #ffffff; color: #14213d; }
    .secondary:hover { background: #f1f5f9; }
    .danger { background: #b42318; }
    .danger:hover { background: #912018; }
    .badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 4px 9px; font-size: 12px; font-weight: 800; background: #e7f5f3; color: #0f766e; white-space: nowrap; }
    .badge.warn { background: #fff4e5; color: #92400e; }
    .badge.stop { background: #fee4e2; color: #b42318; }
    .repair-list { display: grid; gap: 8px; min-width: 240px; }
    .repair-item { display: grid; gap: 4px; }
    .chart-row { display: grid; grid-template-columns: 86px 1fr 92px; gap: 10px; align-items: center; margin: 12px 0; font-size: 13px; }
    .track { height: 12px; border-radius: 999px; background: #e5eaf2; overflow: hidden; }
    .bar-fill { height: 100%; background: #0f766e; }
    details { border-top: 1px solid #e5eaf2; padding-top: 10px; margin-top: 10px; }
    summary { cursor: pointer; font-weight: 800; color: #14213d; }
    .admin-modal {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 22px;
        background: rgba(2, 6, 23, .72);
        backdrop-filter: blur(8px);
    }
    .admin-modal.is-open { display: flex; }
    .admin-modal-panel {
        width: min(920px, 100%);
        max-height: min(820px, calc(100vh - 44px));
        overflow: auto;
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 28px 70px rgba(0, 0, 0, .42);
    }
    .admin-modal-head {
        position: sticky;
        top: 0;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        background: linear-gradient(135deg, #4b5563 0%, #111827 56%, #020617 100%);
        color: #ffffff;
    }
    .admin-modal-head h2 { margin: 0; font-size: 20px; }
    .admin-modal-close {
        width: 38px;
        height: 38px;
        padding: 0;
        border: 1px solid rgba(255, 255, 255, .24);
        background: rgba(255, 255, 255, .08);
        font-size: 22px;
        line-height: 1;
    }
    .admin-modal-body { padding: 20px; }
    .notice { border: 1px solid #bbf7d0; border-radius: 8px; background: #f0fdf4; color: #166534; padding: 12px 14px; }
    .error-box { border: 1px solid #fecaca; border-radius: 8px; background: #fef2f2; color: #991b1b; padding: 12px 14px; }
    @media (max-width: 900px) {
        .hero, .two, .three, .stats, .page-links { grid-template-columns: 1fr; }
        .form-grid { grid-template-columns: 1fr; }
        .span-2, .span-4 { grid-column: auto; }
    }
</style>
