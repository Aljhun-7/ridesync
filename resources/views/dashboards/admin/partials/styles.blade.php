<style>
    .admin-shell { display: grid; gap: 22px; }
    .hero { display: grid; grid-template-columns: 1.35fr .65fr; gap: 18px; align-items: stretch; }
    .hero-main,
    .hero-side,
    .card,
    .page-link,
    .admin-modal-panel {
        border: 1px solid rgba(124, 242, 255, .17);
        border-radius: 8px;
        background: linear-gradient(145deg, rgba(23, 42, 66, .92), rgba(9, 19, 34, .94));
        box-shadow: 16px 16px 34px rgba(1, 8, 18, .58), -10px -10px 24px rgba(124, 242, 255, .08);
    }
    .hero-main {
        position: relative;
        overflow: hidden;
        padding: 26px;
    }
    .hero-main::after {
        content: "";
        position: absolute;
        inset: auto -80px -110px auto;
        width: 260px;
        height: 260px;
        border: 1px solid rgba(32, 247, 165, .28);
        border-radius: 50%;
        box-shadow: inset 14px 14px 26px rgba(0, 0, 0, .38), inset -12px -12px 24px rgba(47, 140, 255, .14), 0 0 36px rgba(32, 247, 165, .14);
    }
    .hero-side {
        color: #ffffff;
        padding: 24px;
        background:
            linear-gradient(135deg, rgba(47, 140, 255, .2), rgba(32, 247, 165, .14)),
            linear-gradient(145deg, rgba(20, 35, 56, .96), rgba(4, 11, 23, .98));
    }
    .hero-side p, .hero-side .muted { color: #a9c5d3; }
    .eyebrow {
        color: #20f7a5;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0;
        text-transform: uppercase;
        text-shadow: 0 0 16px rgba(32, 247, 165, .34);
    }
    .grid { display: grid; gap: 16px; }
    .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .card { padding: 18px; }
    .metric { min-height: 112px; display: flex; flex-direction: column; justify-content: space-between; }
    .metric strong { color: #ffffff; font-size: 28px; line-height: 1; text-shadow: 0 0 18px rgba(47, 140, 255, .28); }
    .muted { color: #8ca8bb; font-size: 13px; }
    .dashboard-chart-grid { display: grid; grid-template-columns: 1.35fr .65fr; gap: 16px; }
    .chart-panel {
        min-height: 340px;
        border: 1px solid rgba(124, 242, 255, .17);
        border-radius: 8px;
        background: linear-gradient(145deg, rgba(23, 42, 66, .92), rgba(9, 19, 34, .94));
        padding: 18px;
        box-shadow: 16px 16px 34px rgba(1, 8, 18, .58), -10px -10px 24px rgba(124, 242, 255, .08);
    }
    .chart-panel-wide { grid-row: span 2; }
    .chart-heading { display: flex; align-items: end; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .chart-heading h2 { margin: 0; color: #ffffff; font-size: 20px; }
    .chart-heading span { color: #8ca8bb; font-size: 13px; }
    .chart-empty {
        min-height: 260px;
        display: grid;
        place-items: center;
        color: #8ca8bb;
        border: 1px dashed rgba(124, 242, 255, .18);
        border-radius: 8px;
        background: rgba(5, 14, 27, .32);
    }
    .page-links { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .page-link {
        display: grid;
        gap: 8px;
        color: #d7f7ff;
        padding: 16px;
        text-decoration: none;
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }
    .page-link:hover {
        transform: translateY(-2px);
        border-color: rgba(32, 247, 165, .56);
        box-shadow: 18px 18px 36px rgba(1, 8, 18, .66), -10px -10px 24px rgba(124, 242, 255, .1), 0 0 22px rgba(32, 247, 165, .12);
    }
    .page-link strong { color: #ffffff; font-size: 16px; }
    .section-title { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 12px; }
    .section-title h2 { margin: 0; color: #ffffff; font-size: 22px; }
    .section-actions { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .table-wrap {
        overflow-x: auto;
        border: 1px solid rgba(124, 242, 255, .12);
        border-radius: 8px;
        background: rgba(5, 14, 27, .38);
        box-shadow: inset 7px 7px 16px rgba(0, 0, 0, .42), inset -6px -6px 14px rgba(124, 242, 255, .045);
    }
    table { width: 100%; min-width: 860px; border-collapse: collapse; }
    th, td { border-bottom: 1px solid rgba(124, 242, 255, .1); padding: 12px 11px; text-align: left; vertical-align: top; font-size: 13px; }
    th { color: #20f7a5; font-size: 12px; text-transform: uppercase; }
    td { color: #d7f7ff; }
    tr:last-child td { border-bottom: 0; }
    .form-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
    label { display: grid; gap: 6px; color: #8ca8bb; font-size: 12px; font-weight: 800; }
    input, select, textarea {
        width: 100%;
        border: 1px solid rgba(124, 242, 255, .16);
        border-radius: 8px;
        padding: 10px 11px;
        color: #eafcff;
        background: rgba(5, 14, 27, .7);
        font: inherit;
        box-shadow: inset 5px 5px 12px rgba(0, 0, 0, .36), inset -4px -4px 10px rgba(124, 242, 255, .06);
    }
    input:focus, select:focus, textarea:focus {
        outline: 0;
        border-color: rgba(32, 247, 165, .72);
        box-shadow: inset 5px 5px 12px rgba(0, 0, 0, .36), 0 0 0 3px rgba(32, 247, 165, .12);
    }
    textarea { min-height: 74px; resize: vertical; }
    .span-2 { grid-column: span 2; }
    .span-4 { grid-column: span 4; }
    .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .inline-form { display: flex; gap: 7px; align-items: center; }
    .inline-form select { min-width: 132px; }
    .secondary { border: 1px solid rgba(124, 242, 255, .18); background: rgba(17, 33, 53, .92); color: #d7f7ff; }
    .secondary:hover { background: rgba(27, 51, 78, .96); }
    .danger { background: linear-gradient(135deg, #ff3d71, #ff8a65); color: #21030d; }
    .danger:hover { filter: brightness(1.06); }
    .badge {
        display: inline-flex;
        align-items: center;
        border: 1px solid rgba(32, 247, 165, .28);
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 800;
        background: rgba(32, 247, 165, .12);
        color: #7cffcf;
        white-space: nowrap;
        box-shadow: inset 2px 2px 5px rgba(0, 0, 0, .26), 0 0 14px rgba(32, 247, 165, .08);
    }
    .badge.warn { border-color: rgba(255, 214, 102, .32); background: rgba(255, 214, 102, .12); color: #ffe08a; }
    .badge.stop { border-color: rgba(255, 61, 113, .36); background: rgba(255, 61, 113, .14); color: #ff9fba; }
    .repair-list { display: grid; gap: 8px; min-width: 240px; }
    .repair-item { display: grid; gap: 4px; }
    .chart-row { display: grid; grid-template-columns: 86px 1fr 92px; gap: 10px; align-items: center; margin: 12px 0; font-size: 13px; }
    .track {
        height: 12px;
        border-radius: 999px;
        background: rgba(5, 14, 27, .82);
        overflow: hidden;
        box-shadow: inset 5px 5px 10px rgba(0, 0, 0, .36), inset -3px -3px 8px rgba(124, 242, 255, .05);
    }
    .bar-fill { height: 100%; background: linear-gradient(90deg, #2f8cff, #20f7a5); box-shadow: 0 0 14px rgba(32, 247, 165, .32); }
    details { border-top: 1px solid rgba(124, 242, 255, .1); padding-top: 10px; margin-top: 10px; }
    summary { cursor: pointer; font-weight: 800; color: #eafcff; }
    .admin-modal {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 22px;
        background: rgba(2, 6, 23, .78);
        backdrop-filter: blur(10px);
    }
    .admin-modal.is-open { display: flex; }
    .admin-modal-panel {
        width: min(920px, 100%);
        max-height: min(820px, calc(100vh - 44px));
        overflow: auto;
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
        border-bottom: 1px solid rgba(124, 242, 255, .14);
        background: linear-gradient(135deg, rgba(47, 140, 255, .22), rgba(32, 247, 165, .13)), rgba(8, 18, 32, .96);
        color: #ffffff;
    }
    .admin-modal-head h2 { margin: 0; font-size: 20px; }
    .admin-modal-close {
        width: 38px;
        height: 38px;
        padding: 0;
        border: 1px solid rgba(124, 242, 255, .24);
        background: rgba(16, 31, 50, .78);
        color: #d7f7ff;
        font-size: 22px;
        line-height: 1;
    }
    .admin-modal-body { padding: 20px; }
    .notice,
    .error-box {
        border-radius: 8px;
        padding: 12px 14px;
        box-shadow: 12px 12px 26px rgba(1, 8, 18, .42), -8px -8px 20px rgba(124, 242, 255, .06);
    }
    .notice { border: 1px solid rgba(32, 247, 165, .28); background: rgba(32, 247, 165, .1); color: #9fffe0; }
    .error-box { border: 1px solid rgba(255, 61, 113, .32); background: rgba(255, 61, 113, .1); color: #ffb2c8; }
    @media (max-width: 900px) {
        .hero, .two, .three, .stats, .dashboard-chart-grid, .page-links { grid-template-columns: 1fr; }
        .chart-panel-wide { grid-row: auto; }
        .form-grid { grid-template-columns: 1fr; }
        .span-2, .span-4 { grid-column: auto; }
        .section-title { align-items: stretch; flex-direction: column; }
    }
</style>
