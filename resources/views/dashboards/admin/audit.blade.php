@extends('layouts.dashboard', ['title' => 'Admin Audit Logs | RideSync'])

@section('content')
    @include('dashboards.admin.partials.styles')

    <div class="admin-shell">
        @include('dashboards.admin.partials.alerts')

        <section class="card">
            <div class="section-title"><h2>Audit Logs</h2><span class="muted">Recent admin activity</span></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Date</th><th>Admin</th><th>Action</th><th>Summary</th><th>IP</th></tr></thead>
                    <tbody>
                        @forelse ($auditLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $log->user?->name ?? 'System' }}</td>
                                <td><span class="badge">{{ ucwords(str_replace(['.', '_'], ' ', $log->action)) }}</span></td>
                                <td>{{ $log->summary }}</td>
                                <td class="muted">{{ $log->ip_address ?: 'Unknown' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No audit logs yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
