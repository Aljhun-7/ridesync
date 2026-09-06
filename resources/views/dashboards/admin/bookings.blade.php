@extends('layouts.dashboard', ['title' => 'Admin Jobs | RideSync'])

@section('content')
    @include('dashboards.admin.partials.styles')

    <div class="admin-shell">
        @include('dashboards.admin.partials.alerts')

        <section class="card">
            <div class="section-title">
                <div><h2>Job Management</h2><span class="muted">Create, edit, update status, and delete bookings</span></div>
                <div class="section-actions"><button type="button" data-modal-open="add-booking-modal">Add Booking</button></div>
            </div>
        </section>

        <div id="add-booking-modal" class="admin-modal" aria-hidden="true">
            <section class="admin-modal-panel" role="dialog" aria-modal="true" aria-labelledby="add-booking-title">
                <div class="admin-modal-head">
                    <h2 id="add-booking-title">Add Booking</h2>
                    <button class="admin-modal-close" type="button" data-modal-close aria-label="Close modal">&times;</button>
                </div>
                <div class="admin-modal-body">
                    <form method="POST" action="{{ route('admin.bookings.store') }}" class="form-grid">
                        @csrf
                        <label>Customer<select name="customer_id"><option value="">Walk-in customer</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->name }}</option>@endforeach</select></label>
                        <label>Walk-in name<input name="customer_name" placeholder="Customer name"></label>
                        <label>Email<input type="email" name="customer_email" placeholder="customer@email.com"></label>
                        <label>Phone<input name="customer_phone" placeholder="Contact number"></label>
                        <label>Vehicle<input name="vehicle" required placeholder="Toyota Vios 2020"></label>
                        <label>Service<select name="service_type" required><option value="Oil Change">Oil Change</option><option value="Brake Repair">Brake Repair</option><option value="Engine Diagnostics">Engine Diagnostics</option><option value="Tire Service">Tire Service</option><option value="Electrical Repair">Electrical Repair</option></select></label>
                        <label>Mechanic<select name="mechanic_id"><option value="">Assign later</option>@foreach ($mechanics as $mechanic)<option value="{{ $mechanic->id }}">{{ $mechanic->name }}</option>@endforeach</select></label>
                        <label>Schedule<input type="datetime-local" name="scheduled_at"></label>
                        <label>Labor<input type="number" step="0.01" min="0" name="labor_cost" value="0"></label>
                        <label>Parts<input type="number" step="0.01" min="0" name="parts_cost" value="0"></label>
                        <label class="span-2">Notes<input name="notes" placeholder="Repair notes"></label>
                        <div class="span-4 actions"><button type="submit">Add Booking</button><button class="secondary" type="button" data-modal-close>Cancel</button></div>
                    </form>
                </div>
            </section>
        </div>

        <section class="card">
            <div class="section-title"><h2>Bookings</h2><span class="muted">{{ $bookings->count() }} total jobs</span></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Job</th><th>Customer</th><th>Vehicle</th><th>Status</th><th>Assigned</th><th>Total</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td><strong>{{ $booking->service_code }}</strong><br><span class="muted">{{ $booking->service_type }} {{ $booking->scheduled_at ? '- '.$booking->scheduled_at->format('M d, Y h:i A') : '' }}</span></td>
                                <td>{{ $booking->customer_name }}<br><span class="muted">{{ $booking->customer_email ?: $booking->customer_phone ?: 'No contact saved' }}</span></td>
                                <td>{{ $booking->vehicle }}</td>
                                <td><span class="badge {{ $booking->status === 'cancelled' ? 'stop' : ($booking->status === 'pending' ? 'warn' : '') }}">{{ $statusLabel($booking->status) }}</span></td>
                                <td>{{ $booking->mechanic?->name ?? 'Unassigned' }}</td>
                                <td>{{ $money($booking->total) }}</td>
                                <td>
                                    <div class="actions">
                                        <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="inline-form">
                                            @csrf @method('PATCH')
                                            <select name="status">@foreach (\App\Models\ServiceBooking::STATUSES as $status)<option value="{{ $status }}" @selected($booking->status === $status)>{{ $statusLabel($status) }}</option>@endforeach</select>
                                            <button type="submit">Update</button>
                                        </form>
                                        <button class="secondary" type="button" data-modal-open="edit-booking-modal-{{ $booking->id }}">Edit</button>
                                        <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form>
                                    </div>
                                    <div id="edit-booking-modal-{{ $booking->id }}" class="admin-modal" aria-hidden="true">
                                        <section class="admin-modal-panel" role="dialog" aria-modal="true" aria-labelledby="edit-booking-title-{{ $booking->id }}">
                                            <div class="admin-modal-head">
                                                <h2 id="edit-booking-title-{{ $booking->id }}">Edit {{ $booking->service_code }}</h2>
                                                <button class="admin-modal-close" type="button" data-modal-close aria-label="Close modal">&times;</button>
                                            </div>
                                            <div class="admin-modal-body">
                                                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="form-grid">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="{{ $booking->status }}">
                                                    <label>Customer<select name="customer_id"><option value="">Walk-in customer</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}" @selected($booking->customer_id === $customer->id)>{{ $customer->name }}</option>@endforeach</select></label>
                                                    <label>Name<input name="customer_name" value="{{ $booking->customer_name }}"></label>
                                                    <label>Email<input type="email" name="customer_email" value="{{ $booking->customer_email }}"></label>
                                                    <label>Phone<input name="customer_phone" value="{{ $booking->customer_phone }}"></label>
                                                    <label>Vehicle<input name="vehicle" value="{{ $booking->vehicle }}" required></label>
                                                    <label>Service<input name="service_type" value="{{ $booking->service_type }}" required></label>
                                                    <label>Mechanic<select name="mechanic_id"><option value="">Assign later</option>@foreach ($mechanics as $mechanic)<option value="{{ $mechanic->id }}" @selected($booking->mechanic_id === $mechanic->id)>{{ $mechanic->name }}</option>@endforeach</select></label>
                                                    <label>Schedule<input type="datetime-local" name="scheduled_at" value="{{ $booking->scheduled_at?->format('Y-m-d\TH:i') }}"></label>
                                                    <label>Labor<input type="number" step="0.01" min="0" name="labor_cost" value="{{ $booking->labor_cost }}"></label>
                                                    <label>Parts<input type="number" step="0.01" min="0" name="parts_cost" value="{{ $booking->parts_cost }}"></label>
                                                    <label class="span-2">Notes<input name="notes" value="{{ $booking->notes }}"></label>
                                                    <div class="span-4 actions"><button type="submit">Save Booking</button><button class="secondary" type="button" data-modal-close>Cancel</button></div>
                                                </form>
                                            </div>
                                        </section>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @include('dashboards.admin.partials.modal-script')
@endsection
