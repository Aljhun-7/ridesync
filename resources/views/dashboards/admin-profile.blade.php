@extends('layouts.dashboard', ['title' => 'Admin Profile | RideSync'])

@section('content')
    <style>
        .profile-shell { display: grid; gap: 18px; }
        .profile-hero,
        .profile-card {
            border: 1px solid rgba(124, 242, 255, .17);
            border-radius: 8px;
            background: linear-gradient(145deg, rgba(23, 42, 66, .92), rgba(9, 19, 34, .94));
            padding: 22px;
            box-shadow: 16px 16px 34px rgba(1, 8, 18, .58), -10px -10px 24px rgba(124, 242, 255, .08);
        }
        .profile-hero { display: flex; justify-content: space-between; gap: 18px; align-items: center; }
        .profile-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .avatar {
            width: 66px;
            height: 66px;
            border-radius: 8px;
            object-fit: contain;
            background: rgba(5, 14, 27, .82);
            padding: 6px;
            box-shadow: inset 5px 5px 12px rgba(0, 0, 0, .42), inset -4px -4px 10px rgba(124, 242, 255, .08), 0 0 20px rgba(32, 247, 165, .12);
        }
        .identity { display: flex; gap: 14px; align-items: center; }
        .profile-card h2 { margin-top: 0; color: #ffffff; }
        .muted { color: #8ca8bb; font-size: 13px; }
        .button-link {
            display: inline-flex;
            border: 1px solid rgba(32, 247, 165, .36);
            border-radius: 8px;
            background: linear-gradient(135deg, #2f8cff, #20f7a5);
            color: #03131e;
            padding: 10px 13px;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 8px 8px 18px rgba(0, 0, 0, .34), -5px -5px 14px rgba(124, 242, 255, .1);
        }
        .button-link:hover { filter: brightness(1.08); transform: translateY(-1px); }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid rgba(124, 242, 255, .1); padding: 11px 8px; text-align: left; vertical-align: top; font-size: 13px; }
        th { color: #20f7a5; font-size: 12px; text-transform: uppercase; }
        td { color: #d7f7ff; }
        tr:last-child td { border-bottom: 0; }
        .table-wrap {
            overflow-x: auto;
            border: 1px solid rgba(124, 242, 255, .12);
            border-radius: 8px;
            background: rgba(5, 14, 27, .38);
            box-shadow: inset 7px 7px 16px rgba(0, 0, 0, .42), inset -6px -6px 14px rgba(124, 242, 255, .045);
        }
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
