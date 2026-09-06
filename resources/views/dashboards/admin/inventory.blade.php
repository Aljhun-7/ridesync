@extends('layouts.dashboard', ['title' => 'Admin Inventory | RideSync'])

@section('content')
    @include('dashboards.admin.partials.styles')

    <div class="admin-shell">
        @include('dashboards.admin.partials.alerts')

        <section class="card">
            <div class="section-title">
                <div><h2>Inventory Management</h2><span class="muted">Add, edit, and manage spare parts</span></div>
                <div class="section-actions"><button type="button" data-modal-open="add-part-modal">Add Part</button></div>
            </div>
            <div id="add-part-modal" class="admin-modal" aria-hidden="true">
                <section class="admin-modal-panel" role="dialog" aria-modal="true" aria-labelledby="add-part-title">
                    <div class="admin-modal-head">
                        <h2 id="add-part-title">Add Part</h2>
                        <button class="admin-modal-close" type="button" data-modal-close aria-label="Close modal">&times;</button>
                    </div>
                    <div class="admin-modal-body">
                        <form method="POST" action="{{ route('admin.parts.store') }}" class="form-grid">
                            @csrf
                            <label>SKU<input name="sku" required></label><label>Name<input name="name" required></label><label>Category<input name="category" required></label><label>Supplier<input name="supplier"></label>
                            <label>Qty<input type="number" min="0" name="quantity" value="0" required></label><label>Reorder at<input type="number" min="0" name="reorder_level" value="5" required></label><label>Cost<input type="number" step="0.01" min="0" name="unit_cost" value="0" required></label><label>Price<input type="number" step="0.01" min="0" name="price" value="0" required></label>
                            <div class="span-4 actions"><button type="submit">Add Part</button><button class="secondary" type="button" data-modal-close>Cancel</button></div>
                        </form>
                    </div>
                </section>
            </div>
            <div class="table-wrap" style="margin-top:18px;">
                <table>
                    <thead><tr><th>Part</th><th>Stock</th><th>Cost</th><th>Price</th><th>Supplier</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse ($parts as $part)
                            <tr>
                                <td><strong>{{ $part->name }}</strong><br><span class="muted">{{ $part->sku }} - {{ $part->category }}</span></td>
                                <td><span class="badge {{ $part->isLowStock() ? 'warn' : '' }}">{{ $part->quantity }} on hand</span><br><span class="muted">Reorder at {{ $part->reorder_level }}</span></td>
                                <td>{{ $money($part->unit_cost) }}</td><td>{{ $money($part->price) }}</td><td>{{ $part->supplier ?: 'Not set' }}</td>
                                <td>
                                    <div class="actions">
                                        <button class="secondary" type="button" data-modal-open="edit-part-modal-{{ $part->id }}">Edit</button>
                                        <form method="POST" action="{{ route('admin.parts.destroy', $part) }}">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form>
                                    </div>
                                    <div id="edit-part-modal-{{ $part->id }}" class="admin-modal" aria-hidden="true">
                                        <section class="admin-modal-panel" role="dialog" aria-modal="true" aria-labelledby="edit-part-title-{{ $part->id }}">
                                            <div class="admin-modal-head">
                                                <h2 id="edit-part-title-{{ $part->id }}">Edit {{ $part->name }}</h2>
                                                <button class="admin-modal-close" type="button" data-modal-close aria-label="Close modal">&times;</button>
                                            </div>
                                            <div class="admin-modal-body">
                                                <form method="POST" action="{{ route('admin.parts.update', $part) }}" class="form-grid">
                                                    @csrf @method('PUT')
                                                    <label>SKU<input name="sku" value="{{ $part->sku }}" required></label><label>Name<input name="name" value="{{ $part->name }}" required></label><label>Category<input name="category" value="{{ $part->category }}" required></label><label>Supplier<input name="supplier" value="{{ $part->supplier }}"></label>
                                                    <label>Qty<input type="number" min="0" name="quantity" value="{{ $part->quantity }}" required></label><label>Reorder<input type="number" min="0" name="reorder_level" value="{{ $part->reorder_level }}" required></label><label>Cost<input type="number" step="0.01" min="0" name="unit_cost" value="{{ $part->unit_cost }}" required></label><label>Price<input type="number" step="0.01" min="0" name="price" value="{{ $part->price }}" required></label>
                                                    <div class="span-4 actions"><button type="submit">Save Part</button><button class="secondary" type="button" data-modal-close>Cancel</button></div>
                                                </form>
                                            </div>
                                        </section>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No spare parts yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @include('dashboards.admin.partials.modal-script')
@endsection
