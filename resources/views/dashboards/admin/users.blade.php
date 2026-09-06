@extends('layouts.dashboard', ['title' => $rolePage['title'].' | RideSync'])

@section('content')
    @include('dashboards.admin.partials.styles')

    <div class="admin-shell">
        @include('dashboards.admin.partials.alerts')
        @include('dashboards.admin.partials.user-management')
    </div>

    @include('dashboards.admin.partials.modal-script')
@endsection
