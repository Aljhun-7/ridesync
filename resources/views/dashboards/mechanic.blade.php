@extends('layouts.dashboard', ['title' => 'Mechanic Dashboard | RideSync'])

@section('content')
    <style>
        .mechanic-shell { display: grid; gap: 22px; }
        .mechanic-hero { display: grid; grid-template-columns: 1.3fr .7fr; gap: 18px; align-items: stretch; }
        .hero-main { border: 1px solid rgba(255, 255, 255, .22); border-radius: 8px; background: linear-gradient(135deg, #ffffff 0%, #eef2f7 100%); padding: 24px; box-shadow: 0 16px 40px rgba(15, 23, 42, .1); }
        .hero-side { border-radius: 8px; background: linear-gradient(135deg, #14532d 0%, #0f766e 48%, #111827 100%); color: #ffffff; padding: 24px; }
        .hero-side p, .hero-side .muted { color: #dbe4f0; }
        .eyebrow { color: #0f766e; font-size: 12px; font-weight: 800; letter-spacing: 0; text-transform: uppercase; }
        .grid { display: grid; gap: 16px; }
        .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .card { border: 1px solid rgba(203, 213, 225, .8); border-radius: 8px; background: rgba(255, 255, 255, .98); padding: 18px; box-shadow: 0 14px 34px rgba(15, 23, 42, .08); }
        .metric { min-height: 110px; display: flex; flex-direction: column; justify-content: space-between; }
        .metric strong { font-size: 28px; line-height: 1; }
        .muted { color: #667085; font-size: 13px; }
        .section-title { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 14px; }
        .section-title h2 { margin: 0; font-size: 22px; }
        .filter-row { display: flex; flex-wrap: wrap; gap: 8px; }
        .filter-link { border: 1px solid #cbd5e1; border-radius: 999px; color: #14213d; background: #ffffff; padding: 8px 12px; font-size: 13px; font-weight: 800; text-decoration: none; }
        .filter-link.is-active { border-color: #0f766e; background: #e7f5f3; color: #0f766e; }
        .repair-grid { display: grid; gap: 14px; }
        .repair-panel { border: 1px solid #dce3ec; border-radius: 8px; background: #f8fafc; padding: 16px; }
        .repair-head { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
        .repair-head h3 { margin: 0 0 5px; font-size: 18px; }
        .repair-body { display: grid; grid-template-columns: .9fr 1.1fr; gap: 16px; margin-top: 14px; }
        .detail-list { display: grid; gap: 8px; }
        .detail-row { display: flex; justify-content: space-between; gap: 12px; border-bottom: 1px solid #eef2f7; padding-bottom: 8px; font-size: 13px; }
        .detail-row span:first-child { color: #667085; font-weight: 800; }
        .work-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        label { display: grid; gap: 5px; color: #667085; font-size: 12px; font-weight: 800; }
        input, select, textarea { width: 100%; border: 1px solid #cbd5e1; border-radius: 7px; padding: 9px 10px; color: #14213d; background: #fff; font: inherit; }
        textarea { min-height: 94px; resize: vertical; }
        .span-2 { grid-column: span 2; }
        .button-row { display: flex; justify-content: flex-end; align-items: center; gap: 10px; }
        .badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 5px 10px; font-size: 12px; font-weight: 800; background: #e7f5f3; color: #0f766e; white-space: nowrap; }
        .badge.warn { background: #fff4e5; color: #92400e; }
        .badge.stop { background: #fee4e2; color: #b42318; }
        .parts-used { display: grid; gap: 7px; margin-top: 12px; }
        .part-line { display: flex; justify-content: space-between; gap: 12px; border: 1px solid #e5eaf2; border-radius: 7px; padding: 9px 10px; font-size: 13px; }
        .notice { border: 1px solid #bbf7d0; border-radius: 8px; background: #f0fdf4; color: #166534; padding: 12px 14px; }
        .error-box { border: 1px solid #fecaca; border-radius: 8px; background: #fef2f2; color: #991b1b; padding: 12px 14px; }
        .chart-row { display: grid; grid-template-columns: 110px 1fr 62px; gap: 10px; align-items: center; margin: 12px 0; font-size: 13px; }
        .track { height: 12px; border-radius: 999px; background: #e5eaf2; overflow: hidden; }
        .bar-fill { height: 100%; background: #0f766e; }
        @media (max-width: 900px) {
            .mechanic-hero, .stats, .two, .repair-body { grid-template-columns: 1fr; }
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
