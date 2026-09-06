@extends('layouts.dashboard', ['title' => 'Mechanic Dashboard | RideSync'])

@section('content')
    <style>
        .mechanic-shell { display: grid; gap: 22px; }
        .mechanic-hero { display: grid; grid-template-columns: 1.3fr .7fr; gap: 18px; align-items: stretch; }
        .hero-main,
        .hero-side,
        .card,
        .repair-panel {
            border: 1px solid rgba(124, 242, 255, .17);
            border-radius: 8px;
            background: linear-gradient(145deg, rgba(23, 42, 66, .92), rgba(9, 19, 34, .94));
            box-shadow: 16px 16px 34px rgba(1, 8, 18, .58), -10px -10px 24px rgba(124, 242, 255, .08);
        }
        .hero-main { position: relative; overflow: hidden; padding: 26px; }
        .hero-main::after {
            content: "";
            position: absolute;
            right: -74px;
            bottom: -96px;
            width: 230px;
            height: 230px;
            border: 1px solid rgba(32, 247, 165, .28);
            border-radius: 50%;
            box-shadow: inset 14px 14px 26px rgba(0, 0, 0, .38), inset -12px -12px 24px rgba(47, 140, 255, .14), 0 0 36px rgba(32, 247, 165, .14);
        }
        .hero-side {
            color: #ffffff;
            padding: 24px;
            background:
                linear-gradient(135deg, rgba(47, 140, 255, .22), rgba(32, 247, 165, .16)),
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
        .card { padding: 18px; }
        .metric { min-height: 110px; display: flex; flex-direction: column; justify-content: space-between; }
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
            border: 1px solid rgba(124, 242, 255, .17);
            border-radius: 8px;
            background: linear-gradient(145deg, rgba(23, 42, 66, .92), rgba(9, 19, 34, .94));
            color: #d7f7ff;
            padding: 16px;
            text-decoration: none;
            box-shadow: 16px 16px 34px rgba(1, 8, 18, .58), -10px -10px 24px rgba(124, 242, 255, .08);
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
        }
        .page-link:hover {
            transform: translateY(-2px);
            border-color: rgba(32, 247, 165, .56);
            box-shadow: 18px 18px 36px rgba(1, 8, 18, .66), -10px -10px 24px rgba(124, 242, 255, .1), 0 0 22px rgba(32, 247, 165, .12);
        }
        .page-link strong { color: #ffffff; font-size: 16px; }
        .section-title { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 14px; }
        .section-title h2 { margin: 0; color: #ffffff; font-size: 22px; }
        .filter-row { display: flex; flex-wrap: wrap; gap: 8px; }
        .filter-link {
            border: 1px solid rgba(124, 242, 255, .18);
            border-radius: 999px;
            color: #d7f7ff;
            background: rgba(16, 31, 50, .82);
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 7px 7px 16px rgba(0, 0, 0, .3), -5px -5px 12px rgba(124, 242, 255, .07);
        }
        .filter-link.is-active { border-color: rgba(32, 247, 165, .74); background: linear-gradient(135deg, #2f8cff, #20f7a5); color: #03131e; }
        .repair-grid { display: grid; gap: 14px; }
        .repair-panel { padding: 16px; }
        .repair-head { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
        .repair-head h3 { margin: 0 0 5px; color: #ffffff; font-size: 18px; }
        .repair-body { display: grid; grid-template-columns: .9fr 1.1fr; gap: 16px; margin-top: 14px; }
        .detail-list { display: grid; gap: 8px; }
        .detail-row { display: flex; justify-content: space-between; gap: 12px; border-bottom: 1px solid rgba(124, 242, 255, .1); padding-bottom: 8px; font-size: 13px; }
        .detail-row span:first-child { color: #8ca8bb; font-weight: 800; }
        .work-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
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
        textarea { min-height: 94px; resize: vertical; }
        .span-2 { grid-column: span 2; }
        .button-row { display: flex; justify-content: flex-end; align-items: center; gap: 10px; }
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
        .parts-used { display: grid; gap: 7px; margin-top: 12px; }
        .part-line {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid rgba(124, 242, 255, .13);
            border-radius: 8px;
            padding: 9px 10px;
            font-size: 13px;
            background: rgba(5, 14, 27, .42);
            box-shadow: inset 4px 4px 10px rgba(0, 0, 0, .24), inset -3px -3px 8px rgba(124, 242, 255, .05);
        }
        .notice,
        .error-box {
            border-radius: 8px;
            padding: 12px 14px;
            box-shadow: 12px 12px 26px rgba(1, 8, 18, .42), -8px -8px 20px rgba(124, 242, 255, .06);
        }
        .notice { border: 1px solid rgba(32, 247, 165, .28); background: rgba(32, 247, 165, .1); color: #9fffe0; }
        .error-box { border: 1px solid rgba(255, 61, 113, .32); background: rgba(255, 61, 113, .1); color: #ffb2c8; }
        .chart-row { display: grid; grid-template-columns: 110px 1fr 62px; gap: 10px; align-items: center; margin: 12px 0; font-size: 13px; }
        .track {
            height: 12px;
            border-radius: 999px;
            background: rgba(5, 14, 27, .82);
            overflow: hidden;
            box-shadow: inset 5px 5px 10px rgba(0, 0, 0, .36), inset -3px -3px 8px rgba(124, 242, 255, .05);
        }
        .bar-fill { height: 100%; background: linear-gradient(90deg, #2f8cff, #20f7a5); box-shadow: 0 0 14px rgba(32, 247, 165, .32); }
        @media (max-width: 900px) {
            .mechanic-hero, .stats, .two, .dashboard-chart-grid, .page-links, .repair-body { grid-template-columns: 1fr; }
            .chart-panel-wide { grid-row: auto; }
            .work-form { grid-template-columns: 1fr; }
            .span-2 { grid-column: auto; }
            .section-title, .repair-head { flex-direction: column; align-items: stretch; }
        }
    </style>

    <div class="mechanic-shell">
        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="mechanic-hero">
            <div class="hero-main">
                <div class="eyebrow">Mechanic workspace</div>
                <h1>Assigned Repair Dashboard</h1>
                <p>Track assigned jobs, update repair progress, record notes, and charge used parts from one workspace.</p>
            </div>
            <div class="hero-side">
                <div class="eyebrow">Today</div>
                <h2>{{ $stats['active'] }} active repairs</h2>
                <p>{{ $stats['completed'] }} completed repairs with {{ $stats['completion_rate'] }}% completion rate.</p>
            </div>
        </section>

        <section class="grid stats">
            <div class="card metric"><span class="muted">Assigned Repairs</span><strong>{{ $stats['assigned'] }}</strong><span class="muted">All jobs assigned to you</span></div>
            <div class="card metric"><span class="muted">Active Repairs</span><strong>{{ $stats['active'] }}</strong><span class="muted">Assigned or in progress</span></div>
            <div class="card metric"><span class="muted">Completion Rate</span><strong>{{ $stats['completion_rate'] }}%</strong><span class="muted">Completed non-cancelled work</span></div>
            <div class="card metric"><span class="muted">Earnings</span><strong>{{ $money($stats['earnings']) }}</strong><span class="muted">Completed labor total</span></div>
        </section>

        @php
            $mechanicChartData = [
                'statusMix' => collect(\App\Models\ServiceBooking::STATUSES)
                    ->map(fn ($status) => ['label' => $statusLabel($status), 'value' => $statusCounts[$status] ?? 0])
                    ->filter(fn ($item) => $item['value'] > 0)
                    ->values(),
                'performance' => collect([
                    ['label' => 'Active', 'value' => $stats['active']],
                    ['label' => 'Completed', 'value' => $stats['completed']],
                    ['label' => 'Parts Used', 'value' => $stats['parts_handled']],
                ])->filter(fn ($item) => $item['value'] > 0)->values(),
            ];
        @endphp
        <script id="mechanic-chart-data" type="application/json">@json($mechanicChartData)</script>
        <section id="mechanic-overview-charts" aria-label="Mechanic dashboard charts"></section>

        <section class="page-links" aria-label="Mechanic workspace pages">
            <a class="page-link" href="{{ route('mechanic.repairs.index') }}"><strong>My Repairs</strong><span class="muted">Review assigned jobs and update repair progress.</span></a>
            <a class="page-link" href="{{ route('mechanic.repairs.index', ['status' => 'assigned']) }}"><strong>Assigned Queue</strong><span class="muted">Open repairs waiting for hands-on work.</span></a>
            <a class="page-link" href="{{ route('mechanic.repairs.index', ['status' => 'in_progress']) }}"><strong>In Progress</strong><span class="muted">Continue active service jobs and notes.</span></a>
            <a class="page-link" href="{{ route('mechanic.performance') }}"><strong>Performance</strong><span class="muted">Check completion rate, earnings, and parts handled.</span></a>
        </section>

        <section class="card">
            <div class="section-title">
                <div>
                    <h2>My Repairs</h2>
                    <span class="muted">Filter assigned repairs by status and open each job to work on it.</span>
                </div>
                <div class="filter-row">
                    <a class="filter-link {{ $statusFilter === null ? 'is-active' : '' }}" href="{{ route('mechanic.repairs.index') }}">All {{ $stats['assigned'] }}</a>
                    @foreach (\App\Models\ServiceBooking::STATUSES as $status)
                        <a class="filter-link {{ $statusFilter === $status ? 'is-active' : '' }}" href="{{ route('mechanic.repairs.index', ['status' => $status]) }}">
                            {{ $statusLabel($status) }} {{ $statusCounts[$status] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="repair-grid">
                @forelse ($repairs as $repair)
                    <article class="repair-panel">
                        <div class="repair-head">
                            <div>
                                <h3>{{ $repair->service_code }} - {{ $repair->service_type }}</h3>
                                <p>{{ $repair->vehicle }} for {{ $repair->customer?->name ?? $repair->customer_name }}</p>
                            </div>
                            <span class="badge {{ $repair->status === 'cancelled' ? 'stop' : (in_array($repair->status, ['pending', 'assigned'], true) ? 'warn' : '') }}">{{ $statusLabel($repair->status) }}</span>
                        </div>

                        <div class="repair-body">
                            <div>
                                <div class="detail-list">
                                    <div class="detail-row"><span>Schedule</span><strong>{{ $repair->scheduled_at?->format('M d, Y h:i A') ?? 'Not scheduled' }}</strong></div>
                                    <div class="detail-row"><span>Customer Email</span><strong>{{ $repair->customer_email ?? 'None' }}</strong></div>
                                    <div class="detail-row"><span>Labor</span><strong>{{ $money($repair->labor_cost) }}</strong></div>
                                    <div class="detail-row"><span>Parts</span><strong>{{ $money($repair->parts_cost) }}</strong></div>
                                    <div class="detail-row"><span>Total</span><strong>{{ $money($repair->total) }}</strong></div>
                                </div>

                                <div class="parts-used">
                                    <strong>Parts Used</strong>
                                    @forelse ($repair->partUsages as $usage)
                                        <div class="part-line">
                                            <span>{{ $usage->part?->name ?? 'Deleted part' }} x {{ $usage->quantity }}</span>
                                            <strong>{{ $money($usage->total_price) }}</strong>
                                        </div>
                                    @empty
                                        <span class="muted">No parts tracked for this repair yet.</span>
                                    @endforelse
                                </div>
                            </div>

                            <form class="work-form" method="POST" action="{{ route('mechanic.repairs.work', $repair) }}">
                                @csrf
                                @method('PATCH')
                                <label>Status
                                    <select name="status">
                                        @foreach (['assigned', 'in_progress', 'completed', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($repair->status === $status)>{{ $statusLabel($status) }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label>Add Part Used
                                    <select name="spare_part_id">
                                        <option value="">No part</option>
                                        @foreach ($parts as $part)
                                            <option value="{{ $part->id }}">{{ $part->name }} - {{ $money($part->price) }} - Stock {{ $part->quantity }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label>Quantity
                                    <input name="quantity" type="number" min="1" max="999" placeholder="1">
                                </label>
                                <label class="span-2">Repair Notes
                                    <textarea name="notes" placeholder="Diagnostics, completed work, next steps">{{ old('notes', $repair->notes) }}</textarea>
                                </label>
                                <div class="span-2 button-row">
                                    <span class="muted">Saving updates status, notes, and any selected part usage.</span>
                                    <button type="submit">Update Repair</button>
                                </div>
                            </form>
                        </div>
                    </article>
                @empty
                    <p>No repairs match this filter yet.</p>
                @endforelse
            </div>
        </section>

        <section class="grid two">
            <div class="card">
                <div class="section-title">
                    <div><h2>Performance Metrics</h2><span class="muted">Completion rates by assigned repair status.</span></div>
                </div>
                @foreach (\App\Models\ServiceBooking::STATUSES as $status)
                    @php
                        $percentage = $stats['assigned'] > 0 ? round(($statusCounts[$status] / $stats['assigned']) * 100) : 0;
                    @endphp
                    <div class="chart-row">
                        <strong>{{ $statusLabel($status) }}</strong>
                        <div class="track"><div class="bar-fill" style="width: {{ $percentage }}%"></div></div>
                        <span class="muted">{{ $percentage }}%</span>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <div class="section-title">
                    <div><h2>Earnings</h2><span class="muted">Completed labor and tracked parts handled.</span></div>
                </div>
                <div class="grid two">
                    <div class="metric"><span class="muted">Completed Labor</span><strong>{{ $money($stats['earnings']) }}</strong><span class="muted">Based on completed repairs</span></div>
                    <div class="metric"><span class="muted">Parts Used</span><strong>{{ $stats['parts_handled'] }}</strong><span class="muted">Total quantity recorded</span></div>
                </div>
            </div>
        </section>
    </div>
@endsection
