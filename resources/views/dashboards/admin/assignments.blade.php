@extends('layouts.dashboard', ['title' => 'Admin Assignments | RideSync'])

@section('content')
    @include('dashboards.admin.partials.styles')

    <div class="admin-shell">
        @include('dashboards.admin.partials.alerts')

        <section class="card">
            <div class="section-title"><h2>Assignments</h2><span class="muted">Assign mechanics and update active work</span></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Job</th><th>Vehicle</th><th>Status</th><th>Mechanic</th><th>Schedule</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td><strong>{{ $booking->service_code }}</strong><br><span class="muted">{{ $booking->service_type }}</span></td>
                                <td>{{ $booking->vehicle }}</td>
                                <td><span class="badge {{ $booking->status === 'cancelled' ? 'stop' : ($booking->status === 'pending' ? 'warn' : '') }}">{{ $statusLabel($booking->status) }}</span></td>
                                <td>{{ $booking->mechanic?->name ?? 'Unassigned' }}</td>
                                <td>{{ $booking->scheduled_at?->format('M d, Y h:i A') ?? 'Not scheduled' }}</td>
                                <td>
                                    <div class="actions">
                                        <form method="POST" action="{{ route('admin.bookings.assign', $booking) }}" class="inline-form">
                                            @csrf @method('PATCH')
                                            <select name="mechanic_id">@foreach ($mechanics as $mechanic)<option value="{{ $mechanic->id }}" @selected($booking->mechanic_id === $mechanic->id)>{{ $mechanic->name }}</option>@endforeach</select>
                                            <button type="submit">Assign</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="inline-form">
                                            @csrf @method('PATCH')
                                            <select name="status">@foreach (\App\Models\ServiceBooking::STATUSES as $status)<option value="{{ $status }}" @selected($booking->status === $status)>{{ $statusLabel($status) }}</option>@endforeach</select>
                                            <button type="submit">Update</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No bookings to assign yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
