<section class="card">
    <div class="section-title"><h2>{{ $rolePage['title'] }}</h2><span class="muted">{{ $rolePage['subtitle'] }}</span></div>
    @if ($rolePage['allowCreate'])
        <form method="POST" action="{{ $rolePage['store'] }}" class="form-grid">
            @csrf
            <label>Name<input name="name" required></label>
            <label>Email<input type="email" name="email" required></label>
            <label>Password<input type="password" name="password" required></label>
            <label>Confirm<input type="password" name="password_confirmation" required></label>
            <div class="span-4"><button type="submit">Add {{ $rolePage['singular'] }}</button></div>
        </form>
    @endif
    <div class="table-wrap" style="margin-top:18px;">
        <table style="min-width:620px;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    @if ($rolePage['showRepairs'])
                        <th>Repair Status</th>
                    @endif
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rolePage['records'] as $record)
                    <tr>
                        <td>{{ $record->name }}</td><td>{{ $record->email }}</td><td>{{ $record->created_at->format('M d, Y') }}</td>
                        @if ($rolePage['showRepairs'])
                            <td>
                                <div class="repair-list">
                                    @forelse (($rolePage['repairs'][$record->id] ?? collect()) as $repair)
                                        <div class="repair-item">
                                            <span><strong>{{ $repair->service_code }}</strong> - {{ $repair->service_type }} - {{ $repair->vehicle }}</span>
                                            <span>
                                                <span class="badge {{ $repair->status === 'cancelled' ? 'stop' : ($repair->status === 'pending' ? 'warn' : '') }}">{{ $statusLabel($repair->status) }}</span>
                                                <span class="muted">{{ $repair->mechanic?->name ? 'Mechanic: '.$repair->mechanic->name : 'Unassigned' }}</span>
                                            </span>
                                        </div>
                                    @empty
                                        <span class="muted">No repair records yet.</span>
                                    @endforelse
                                </div>
                            </td>
                        @endif
                        <td>
                            <div class="actions">
                                <button class="secondary" type="button" data-modal-open="edit-{{ strtolower($rolePage['singular']) }}-modal-{{ $record->id }}">Edit</button>
                                <form method="POST" action="{{ route($rolePage['delete'], $record) }}">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form>
                            </div>
                            <div id="edit-{{ strtolower($rolePage['singular']) }}-modal-{{ $record->id }}" class="admin-modal" aria-hidden="true">
                                <section class="admin-modal-panel" role="dialog" aria-modal="true" aria-labelledby="edit-{{ strtolower($rolePage['singular']) }}-title-{{ $record->id }}">
                                    <div class="admin-modal-head">
                                        <h2 id="edit-{{ strtolower($rolePage['singular']) }}-title-{{ $record->id }}">Edit {{ $record->name }}</h2>
                                        <button class="admin-modal-close" type="button" data-modal-close aria-label="Close modal">&times;</button>
                                    </div>
                                    <div class="admin-modal-body">
                                        <form method="POST" action="{{ route($rolePage['update'], $record) }}" class="form-grid">
                                            @csrf @method('PUT')
                                            <label>Name<input name="name" value="{{ $record->name }}" required></label>
                                            <label>Email<input type="email" name="email" value="{{ $record->email }}" required></label>
                                            <label>Password<input type="password" name="password"></label>
                                            <label>Confirm<input type="password" name="password_confirmation"></label>
                                            <div class="span-4 actions"><button type="submit">Save Record</button><button class="secondary" type="button" data-modal-close>Cancel</button></div>
                                        </form>
                                    </div>
                                </section>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $rolePage['showRepairs'] ? 5 : 4 }}">No records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
