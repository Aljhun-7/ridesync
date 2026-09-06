@extends('layouts.dashboard', ['title' => 'Admin Profile | RideSync'])

@section('content')
    <style>
        .profile-shell { display: grid; gap: 18px; }
        .profile-hero, .profile-card { border: 1px solid rgba(203, 213, 225, .28); border-radius: 8px; background: rgba(255, 255, 255, .95); padding: 22px; box-shadow: 0 16px 42px rgba(0, 0, 0, .16); }
        .profile-hero { display: flex; justify-content: space-between; gap: 18px; align-items: center; }
        .profile-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .avatar { width: 66px; height: 66px; border-radius: 8px; object-fit: contain; background: #0f172a; padding: 6px; }
        .identity { display: flex; gap: 14px; align-items: center; }
        .muted { color: #667085; font-size: 13px; }
        .button-link { display: inline-flex; border: 1px solid #cbd5e1; border-radius: 7px; background: #fff; color: #14213d; padding: 9px 12px; font-size: 13px; font-weight: 800; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5eaf2; padding: 11px 8px; text-align: left; vertical-align: top; font-size: 13px; }
        th { color: #667085; font-size: 12px; text-transform: uppercase; }
        .table-wrap { overflow-x: auto; }
        @media (max-width: 800px) {
            .profile-hero { align-items: flex-start; flex-direction: column; }
            .profile-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="profile-shell">
        <section class="profile-hero">
            <div class="identity">
                <img class="avatar" src="{{ asset('images/logo.png') }}" alt="RideSync logo">
                <div>
                    <h1>{{ $admin->name }}</h1>
                    <p>{{ $admin->email }} - {{ ucfirst($admin->role) }}</p>
                </div>
            </div>
            <a class="button-link" href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
        </section>

        <section class="profile-grid">
            <div class="profile-card">
                <h2>Login Logs</h2>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Date</th><th>IP</th><th>Device</th></tr></thead>
                        <tbody>
                            @forelse ($loginLogs as $log)
                                <tr>
                                    <td>{{ $log->logged_in_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $log->ip_address ?: 'Unknown' }}</td>
                                    <td class="muted">{{ $log->user_agent ?: 'Unknown device' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">No login logs yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="profile-card">
                <h2>Audit History</h2>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Date</th><th>Action</th><th>Summary</th></tr></thead>
                        <tbody>
                            @forelse ($auditLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ ucwords(str_replace(['.', '_'], ' ', $log->action)) }}</td>
                                    <td class="muted">{{ $log->summary }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">No audit activity yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
