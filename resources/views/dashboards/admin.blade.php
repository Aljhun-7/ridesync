@extends('layouts.dashboard', ['title' => 'Admin Dashboard | RideSync'])

@section('content')
    @include('dashboards.admin.partials.styles')

    <div class="admin-shell">
        @include('dashboards.admin.partials.alerts')

        <section class="hero">
            <div class="hero-main">
                <div class="eyebrow">Admin operations</div>
                <h1>RideSync Command Center</h1>
                <p>Monitor the shop at a glance, then open each admin feature from its own page using the navigation bar.</p>
            </div>
            <div class="hero-side">
                <div class="muted">Completed revenue</div>
                <h1>{{ $money($stats['revenue']) }}</h1>
                <p>{{ $stats['active'] }} active jobs, {{ $stats['pending'] }} pending, {{ $stats['low_stock'] }} low-stock parts.</p>
            </div>
        </section>

        <section class="grid stats" aria-label="Dashboard statistics">
            <div class="card metric"><span class="muted">Bookings</span><strong>{{ $stats['bookings'] }}</strong><span>Total service jobs</span></div>
            <div class="card metric"><span class="muted">Pending</span><strong>{{ $stats['pending'] }}</strong><span>Need assignment</span></div>
            <div class="card metric"><span class="muted">Completed</span><strong>{{ $stats['completed'] }}</strong><span>Closed repairs</span></div>
            <div class="card metric"><span class="muted">Customers</span><strong>{{ $stats['customers'] }}</strong><span>Managed records</span></div>
        </section>

        <section class="page-links" aria-label="Admin feature pages">
            <a class="page-link" href="{{ route('admin.bookings.index') }}"><strong>Jobs</strong><span class="muted">Create, edit, delete, and update service bookings.</span></a>
            <a class="page-link" href="{{ route('admin.assignments') }}"><strong>Assignments</strong><span class="muted">Assign mechanics and move active jobs forward.</span></a>
            <a class="page-link" href="{{ route('admin.inventory') }}"><strong>Inventory</strong><span class="muted">Manage spare parts, prices, suppliers, and stock levels.</span></a>
            <a class="page-link" href="{{ route('admin.mechanics.index') }}"><strong>Mechanics</strong><span class="muted">Maintain mechanic accounts and access.</span></a>
            <a class="page-link" href="{{ route('admin.customers.index') }}"><strong>Customers</strong><span class="muted">Maintain customer accounts and contact records.</span></a>
            <a class="page-link" href="{{ route('admin.reports') }}"><strong>Reports</strong><span class="muted">Review revenue, service mix, and mechanic performance.</span></a>
            <a class="page-link" href="{{ route('admin.audit') }}"><strong>Audit Logs</strong><span class="muted">Check recent admin activity and system changes.</span></a>
            <a class="page-link" href="{{ route('admin.profile') }}"><strong>Profile</strong><span class="muted">View admin login history and profile activity.</span></a>
        </section>
    </div>
@endsection
