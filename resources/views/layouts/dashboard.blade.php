<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Dashboard | RideSync' }}</title>
        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                color: #14213d;
                background:
                    radial-gradient(circle at top left, rgba(148, 163, 184, .32), transparent 34%),
                    linear-gradient(135deg, #f5f7fb 0%, #d4d8df 48%, #0b0f16 100%);
                background-attachment: fixed;
                font-family: Arial, Helvetica, sans-serif;
            }
            header {
                border-bottom: 1px solid rgba(255, 255, 255, .12);
                background: linear-gradient(135deg, #4b5563 0%, #111827 55%, #020617 100%);
                color: #ffffff;
                box-shadow: 0 14px 36px rgba(0, 0, 0, .24);
            }
            .bar,
            .content {
                width: min(1280px, calc(100% - 32px));
                margin: 0 auto;
            }
            .bar {
                min-height: 70px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
            }
            .brand {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 20px;
                font-weight: 800;
            }
            .brand-logo {
                width: 44px;
                height: 44px;
                border-radius: 8px;
                object-fit: contain;
                background: rgba(255, 255, 255, .08);
                padding: 3px;
            }
            .user {
                color: #cbd5e1;
                font-size: 14px;
            }
            .header-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                align-items: center;
                justify-content: flex-end;
            }
            .admin-tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                padding: 0 0 16px;
            }
            .header-link {
                border: 1px solid rgba(255, 255, 255, .18);
                border-radius: 7px;
                color: #ffffff;
                padding: 10px 14px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 800;
            }
            .admin-tabs .header-link {
                background: rgba(255, 255, 255, .06);
            }
            .admin-tabs .header-link.is-active {
                border-color: rgba(255, 255, 255, .82);
                background: #ffffff;
                color: #14213d;
            }
            .modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 50;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 20px;
                background: rgba(2, 6, 23, .72);
                backdrop-filter: blur(8px);
            }
            .modal-backdrop.is-open {
                display: flex;
            }
            .logout-modal {
                width: min(420px, 100%);
                border: 1px solid rgba(255, 255, 255, .16);
                border-radius: 8px;
                background: linear-gradient(135deg, #ffffff 0%, #eef2f7 48%, #d7dde6 100%);
                color: #0f172a;
                box-shadow: 0 28px 70px rgba(0, 0, 0, .42);
                overflow: hidden;
            }
            .logout-modal-head {
                display: flex;
                gap: 12px;
                align-items: center;
                padding: 18px 20px;
                background: linear-gradient(135deg, #4b5563 0%, #111827 56%, #020617 100%);
                color: #ffffff;
            }
            .logout-modal-logo {
                width: 46px;
                height: 46px;
                border-radius: 8px;
                object-fit: contain;
                background: rgba(255, 255, 255, .1);
                padding: 4px;
            }
            .logout-modal-title {
                margin: 0;
                font-size: 20px;
                line-height: 1.15;
                font-weight: 800;
            }
            .logout-modal-subtitle {
                margin-top: 3px;
                color: #cbd5e1;
                font-size: 13px;
            }
            .logout-modal-body {
                padding: 22px 20px 18px;
            }
            .logout-modal-body p {
                color: #475569;
            }
            .logout-modal-actions {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                padding: 0 20px 20px;
            }
            .modal-cancel {
                border: 1px solid #cbd5e1;
                background: #ffffff;
                color: #14213d;
            }
            .modal-cancel:hover {
                background: #f1f5f9;
            }
            .modal-confirm {
                background: #0f766e;
            }
            .content {
                padding: 32px 0 52px;
            }
            .panel {
                border: 1px solid #d8dee8;
                border-radius: 8px;
                background: #ffffff;
                padding: 32px;
                box-shadow: 0 14px 40px rgba(20, 33, 61, .06);
            }
            h1 {
                margin: 0 0 10px;
                font-size: 34px;
                line-height: 1.2;
            }
            p {
                margin: 0;
                color: #667085;
                line-height: 1.6;
            }
            button {
                border: 0;
                border-radius: 7px;
                padding: 10px 14px;
                color: #ffffff;
                background: #0f766e;
                font-weight: 800;
                cursor: pointer;
            }
            button:hover {
                background: #115e59;
            }
            a {
                color: inherit;
            }
        </style>
    </head>
    <body>
        <header>
            <div class="bar">
                <div>
                    <div class="brand">
                        <img class="brand-logo" src="{{ asset('images/logo.png') }}" alt="RideSync logo">
                        <span>RideSync</span>
                    </div>
                    <div class="user">{{ auth()->user()->name }} - {{ ucfirst(auth()->user()->role) }}</div>
                </div>

                <div class="header-actions">
                    @if (auth()->user()->role === 'admin')
                        <a class="header-link" href="{{ route('admin.profile') }}">Profile</a>
                    @endif
                    <form id="logout-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button id="logout-open" type="button">Log out</button>
                    </form>
                </div>
            </div>
            @if (auth()->user()->role === 'admin')
                @php
                    $adminTabs = [
                        ['label' => 'Overview', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
                        ['label' => 'Jobs', 'route' => 'admin.bookings.index', 'active' => 'admin.bookings.*'],
                        ['label' => 'Assignments', 'route' => 'admin.assignments', 'active' => 'admin.assignments'],
                        ['label' => 'Inventory', 'route' => 'admin.inventory', 'active' => 'admin.inventory'],
                        ['label' => 'Mechanics', 'route' => 'admin.mechanics.index', 'active' => 'admin.mechanics.*'],
                        ['label' => 'Customers', 'route' => 'admin.customers.index', 'active' => 'admin.customers.*'],
                        ['label' => 'Reports', 'route' => 'admin.reports', 'active' => 'admin.reports'],
                        ['label' => 'Audit Logs', 'route' => 'admin.audit', 'active' => 'admin.audit'],
                    ];
                @endphp
                <nav class="bar admin-tabs" aria-label="Admin navigation">
                    @foreach ($adminTabs as $tab)
                        <a class="header-link {{ request()->routeIs($tab['active']) ? 'is-active' : '' }}" href="{{ route($tab['route']) }}">{{ $tab['label'] }}</a>
                    @endforeach
                </nav>
            @elseif (auth()->user()->role === 'mechanic')
                @php
                    $mechanicTabs = [
                        ['label' => 'Dashboard', 'route' => 'mechanic.dashboard', 'active' => 'mechanic.dashboard'],
                        ['label' => 'My Repairs', 'route' => 'mechanic.repairs.index', 'active' => 'mechanic.repairs.*'],
                        ['label' => 'Performance', 'route' => 'mechanic.performance', 'active' => 'mechanic.performance'],
                    ];
                @endphp
                <nav class="bar admin-tabs" aria-label="Mechanic navigation">
                    @foreach ($mechanicTabs as $tab)
                        <a class="header-link {{ request()->routeIs($tab['active']) ? 'is-active' : '' }}" href="{{ route($tab['route']) }}">{{ $tab['label'] }}</a>
                    @endforeach
                </nav>
            @endif
        </header>

        <main class="content">
            @yield('content')
        </main>

        <div id="logout-modal" class="modal-backdrop" aria-hidden="true">
            <section class="logout-modal" role="dialog" aria-modal="true" aria-labelledby="logout-title">
                <div class="logout-modal-head">
                    <img class="logout-modal-logo" src="{{ asset('images/logo.png') }}" alt="RideSync logo">
                    <div>
                        <h2 id="logout-title" class="logout-modal-title">Log out of RideSync?</h2>
                        <div class="logout-modal-subtitle">{{ auth()->user()->name }} - {{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                </div>
                <div class="logout-modal-body">
                    <p>Your dashboard session will end and you will be returned to the login page.</p>
                </div>
                <div class="logout-modal-actions">
                    <button id="logout-cancel" class="modal-cancel" type="button">Cancel</button>
                    <button id="logout-confirm" class="modal-confirm" type="button">Yes, log out</button>
                </div>
            </section>
        </div>

        <script>
            (() => {
                const form = document.getElementById('logout-form');
                const modal = document.getElementById('logout-modal');
                const open = document.getElementById('logout-open');
                const cancel = document.getElementById('logout-cancel');
                const confirm = document.getElementById('logout-confirm');

                if (!form || !modal || !open || !cancel || !confirm) {
                    return;
                }

                const closeModal = () => {
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                    open.focus();
                };

                open.addEventListener('click', () => {
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    cancel.focus();
                });

                cancel.addEventListener('click', closeModal);
                confirm.addEventListener('click', () => form.submit());
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal();
                    }
                });
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                        closeModal();
                    }
                });
            })();
        </script>
    </body>
</html>
