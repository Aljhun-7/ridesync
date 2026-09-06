<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Dashboard | RideSync' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            * { box-sizing: border-box; }
            :root {
                --ink: #eafcff;
                --text: #d7f7ff;
                --muted: #8ca8bb;
                --panel: rgba(15, 29, 47, .9);
                --panel-soft: rgba(22, 39, 61, .82);
                --line: rgba(124, 242, 255, .18);
                --blue: #2f8cff;
                --green: #20f7a5;
                --accent: linear-gradient(135deg, var(--blue), var(--green));
                --shadow-dark: rgba(1, 8, 18, .72);
                --shadow-light: rgba(127, 244, 255, .12);
            }
            body {
                margin: 0;
                min-height: 100vh;
                color: var(--text);
                background:
                    radial-gradient(circle at 12% 8%, rgba(47, 140, 255, .24), transparent 28%),
                    radial-gradient(circle at 86% 4%, rgba(32, 247, 165, .18), transparent 24%),
                    linear-gradient(135deg, #07111f 0%, #101b2c 46%, #071019 100%);
                background-attachment: fixed;
                font-family: Arial, Helvetica, sans-serif;
            }
            body::before {
                content: "";
                position: fixed;
                inset: 0;
                pointer-events: none;
                background-image:
                    linear-gradient(rgba(124, 242, 255, .045) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(124, 242, 255, .04) 1px, transparent 1px);
                background-size: 42px 42px;
                mask-image: linear-gradient(to bottom, rgba(0, 0, 0, .85), transparent 82%);
            }
            header {
                position: sticky;
                top: 0;
                z-index: 20;
                border-bottom: 1px solid var(--line);
                background:
                    linear-gradient(135deg, rgba(20, 35, 55, .94), rgba(4, 10, 22, .94)),
                    linear-gradient(135deg, rgba(47, 140, 255, .16), rgba(32, 247, 165, .1));
                color: #ffffff;
                box-shadow: 14px 14px 32px var(--shadow-dark), -10px -10px 28px var(--shadow-light);
                backdrop-filter: blur(18px);
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
                text-shadow: 0 0 18px rgba(32, 247, 165, .34);
            }
            .brand-logo {
                width: 44px;
                height: 44px;
                border-radius: 8px;
                object-fit: contain;
                background: rgba(10, 22, 38, .86);
                padding: 3px;
                box-shadow: inset 3px 3px 8px rgba(0, 0, 0, .5), inset -3px -3px 8px rgba(124, 242, 255, .12), 0 0 18px rgba(32, 247, 165, .18);
            }
            .user {
                color: var(--muted);
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
                gap: 4px;
                min-height: auto;
                justify-content: flex-start;
                padding: 0 0 16px;
            }
            .header-link {
                position: relative;
                border: 1px solid transparent;
                border-radius: 8px;
                color: var(--text);
                padding: 10px 14px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 800;
                transition: color .18s ease, transform .18s ease, text-shadow .18s ease;
            }
            .admin-tabs .header-link {
                background: transparent;
                box-shadow: none;
            }
            .admin-tabs .header-link.is-active {
                color: #eafff9;
                text-shadow: 0 0 16px rgba(32, 247, 165, .5);
            }
            .admin-tabs .header-link.is-active::after {
                content: "";
                position: absolute;
                right: 12px;
                bottom: 4px;
                left: 12px;
                height: 3px;
                border-radius: 999px;
                background: var(--accent);
                box-shadow: 0 0 16px rgba(32, 247, 165, .45);
            }
            .header-link:hover {
                transform: translateY(-1px);
                color: #ffffff;
                text-shadow: 0 0 14px rgba(47, 140, 255, .4);
            }
            .header-actions .header-link {
                border-color: var(--line);
                background: rgba(16, 31, 50, .78);
                box-shadow: 8px 8px 18px rgba(0, 0, 0, .32), -6px -6px 16px rgba(124, 242, 255, .08);
            }
            .modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 50;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 20px;
                background: rgba(2, 6, 23, .76);
                backdrop-filter: blur(8px);
            }
            .modal-backdrop.is-open {
                display: flex;
            }
            .logout-modal {
                width: min(420px, 100%);
                border: 1px solid var(--line);
                border-radius: 8px;
                background: linear-gradient(145deg, rgba(20, 36, 57, .98), rgba(7, 16, 30, .98));
                color: var(--text);
                box-shadow: 22px 22px 60px rgba(0, 0, 0, .58), -12px -12px 34px rgba(124, 242, 255, .09);
                overflow: hidden;
            }
            .logout-modal-head {
                display: flex;
                gap: 12px;
                align-items: center;
                padding: 18px 20px;
                background: linear-gradient(135deg, rgba(47, 140, 255, .22), rgba(32, 247, 165, .14));
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
                color: var(--muted);
                font-size: 13px;
            }
            .logout-modal-body {
                padding: 22px 20px 18px;
            }
            .logout-modal-body p {
                color: var(--muted);
            }
            .logout-modal-actions {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                padding: 0 20px 20px;
            }
            .modal-cancel {
                border: 1px solid var(--line);
                background: rgba(16, 31, 50, .9);
                color: var(--text);
            }
            .modal-cancel:hover {
                background: rgba(25, 46, 72, .94);
            }
            .modal-confirm {
                background: var(--accent);
                color: #03131e;
            }
            .content {
                padding: 32px 0 52px;
            }
            .panel {
                border: 1px solid var(--line);
                border-radius: 8px;
                background: var(--panel);
                padding: 32px;
                box-shadow: 16px 16px 36px var(--shadow-dark), -10px -10px 28px var(--shadow-light);
            }
            h1 {
                margin: 0 0 10px;
                font-size: 34px;
                line-height: 1.2;
            }
            p {
                margin: 0;
                color: var(--muted);
                line-height: 1.6;
            }
            button {
                border: 0;
                border-radius: 8px;
                padding: 10px 14px;
                color: #03131e;
                background: var(--accent);
                font-weight: 800;
                cursor: pointer;
                box-shadow: 8px 8px 18px rgba(0, 0, 0, .34), -5px -5px 14px rgba(124, 242, 255, .1), inset 1px 1px 3px rgba(255, 255, 255, .32);
                transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
            }
            button:hover {
                filter: brightness(1.08);
                transform: translateY(-1px);
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
