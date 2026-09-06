@extends('layouts.dashboard', ['title' => 'Admin Reports | RideSync'])

@section('content')
    @include('dashboards.admin.partials.styles')

    <div class="admin-shell">
        @include('dashboards.admin.partials.alerts')

        <section class="grid stats" aria-label="Report statistics">
            <div class="card metric"><span class="muted">Revenue</span><strong>{{ $money($stats['revenue']) }}</strong><span>Completed jobs</span></div>
            <div class="card metric"><span class="muted">Completed</span><strong>{{ $stats['completed'] }}</strong><span>Closed repairs</span></div>
            <div class="card metric"><span class="muted">Active</span><strong>{{ $stats['active'] }}</strong><span>Assigned or in progress</span></div>
            <div class="card metric"><span class="muted">Low Stock</span><strong>{{ $stats['low_stock'] }}</strong><span>Parts needing reorder</span></div>
        </section>

        <section class="grid two">
            <div class="card">
                <div class="section-title"><h2>Revenue Chart</h2><span class="muted">By month</span></div>
                @forelse ($monthlyRevenue as $month => $revenue)
                    <div class="chart-row">
                        <span>{{ $month }}</span>
                        <div class="track"><div class="bar-fill" style="width: {{ max(3, ($revenue / $maxMonthlyRevenue) * 100) }}%;"></div></div>
                        <strong>{{ $money($revenue) }}</strong>
                    </div>
                @empty
                    <p>No completed revenue yet.</p>
                @endforelse
            </div>
            <div class="card">
                <div class="section-title"><h2>Service Reports</h2><span class="muted">Revenue by service type</span></div>
                @forelse ($serviceRevenue as $service => $revenue)
                    <div class="chart-row">
                        <span>{{ $service }}</span>
                        <div class="track"><div class="bar-fill" style="width: {{ max(3, ($revenue / $maxServiceRevenue) * 100) }}%;"></div></div>
                        <strong>{{ $money($revenue) }}</strong>
                    </div>
                @empty
                    <p>No service revenue to report yet.</p>
                @endforelse
            </div>
        </section>

        <section class="card">
            <div class="section-title"><h2>Mechanic Performance</h2><span class="muted">Completed work and revenue</span></div>
            <div class="grid three">
                @forelse ($mechanicPerformance as $row)
                    <div>
                        <strong>{{ $row['mechanic']->name }}</strong>
                        <p>{{ $row['active'] }} active, {{ $row['completed'] }} completed, {{ $money($row['revenue']) }} revenue.</p>
                    </div>
                @empty
                    <p>No mechanics to report yet.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
