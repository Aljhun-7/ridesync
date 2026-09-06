<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'RideSync' }}</title>
        <style>
            :root {
                --bg: #f6f8fb;
                --panel: #ffffff;
                --ink: #152033;
                --muted: #667085;
                --line: #d8dee8;
                --brand: #0ea5e9;
                --brand-dark: #047857;
                --accent: #22c55e;
                --brand-gradient: linear-gradient(135deg, #0ea5e9 0%, #14b8a6 52%, #22c55e 100%);
                --brand-glow: rgba(20, 184, 166, .24);
                --danger: #b42318;
                --ok: #047857;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                color: var(--ink);
                background:
                    linear-gradient(rgba(12, 20, 30, .58), rgba(12, 20, 30, .58)),
                    url('{{ asset('images/bg.jpg') }}') center / cover no-repeat fixed;
                font-family: Arial, Helvetica, sans-serif;
            }

            a {
                color: var(--brand-dark);
                font-weight: 700;
                text-decoration: none;
            }

            a:hover {
                color: #0ea5e9;
            }

            .shell {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 28px;
            }

            .form-panel {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .form-card {
                position: relative;
                width: 100%;
                max-width: 390px;
                background: rgba(255, 255, 255, .94);
                border: 1px solid rgba(255, 255, 255, .68);
                border-radius: 8px;
                padding: 28px;
                box-shadow:
                    0 0 0 2px rgba(20, 184, 166, .42),
                    0 0 24px rgba(14, 165, 233, .28),
                    0 0 34px rgba(34, 197, 94, .18),
                    0 22px 70px rgba(4, 13, 25, .32);
                backdrop-filter: blur(8px);
            }

            .form-card::before {
                content: "";
                position: absolute;
                inset: -4px;
                z-index: -1;
                border-radius: 12px;
                background: var(--brand-gradient);
                filter: blur(8px);
                opacity: .72;
            }

            .form-card::after {
                content: "";
                position: absolute;
                inset: 0;
                z-index: -1;
                border-radius: 8px;
                background: rgba(255, 255, 255, .94);
            }

            .auth-logo {
                display: block;
                width: 96px;
                max-width: 36%;
                max-height: 100px;
                height: auto;
                object-fit: contain;
                margin: 0 auto 20px;
                filter: drop-shadow(0 10px 20px rgba(20, 184, 166, .32));
            }

            h2 {
                margin: 0 0 8px;
                font-size: 28px;
                line-height: 1.2;
                text-align: center;
            }

            .subtitle {
                margin: 0 0 24px;
                color: var(--muted);
                line-height: 1.5;
                text-align: center;
            }

            label {
                display: block;
                margin: 16px 0 7px;
                font-size: 14px;
                font-weight: 700;
            }

            input,
            select {
                width: 100%;
                height: 46px;
                border: 1px solid var(--line);
                border-radius: 7px;
                padding: 0 12px;
                color: var(--ink);
                background: #ffffff;
                outline: none;
            }

            .password-field {
                position: relative;
            }

            .password-field input {
                padding-right: 48px;
            }

            .password-toggle {
                position: absolute;
                top: 50%;
                right: 10px;
                width: 32px;
                height: 32px;
                padding: 0;
                border: 0;
                border-radius: 7px;
                color: #047857;
                background: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .password-toggle:hover,
            .password-toggle:focus {
                color: #0ea5e9;
                background: rgba(20, 184, 166, .12);
                outline: none;
            }

            .password-toggle svg {
                width: 20px;
                height: 20px;
                stroke: currentColor;
                stroke-width: 2;
                stroke-linecap: round;
                stroke-linejoin: round;
                fill: none;
            }

            .password-toggle .eye-off-icon {
                display: none;
            }

            .password-toggle.is-visible .eye-icon {
                display: none;
            }

            .password-toggle.is-visible .eye-off-icon {
                display: block;
            }

            input:focus,
            select:focus {
                border-color: #14b8a6;
                box-shadow: 0 0 0 3px var(--brand-glow);
            }

            .check-row {
                display: flex;
                gap: 9px;
                align-items: center;
                margin: 16px 0 2px;
                color: var(--muted);
                font-size: 14px;
            }

            .check-row input {
                width: 16px;
                height: 16px;
            }

            .button {
                width: 100%;
                height: 46px;
                margin-top: 22px;
                border: 0;
                border-radius: 7px;
                color: #ffffff;
                background: var(--brand-gradient);
                font-weight: 800;
                cursor: pointer;
                box-shadow: 0 12px 24px rgba(14, 165, 233, .22);
            }

            .button:hover {
                filter: brightness(.96) saturate(1.08);
                box-shadow: 0 14px 28px rgba(20, 184, 166, .28);
            }

            .helper {
                margin-top: 18px;
                color: var(--muted);
                font-size: 14px;
                text-align: center;
            }

            .error,
            .status {
                border-radius: 7px;
                padding: 10px 12px;
                font-size: 14px;
                line-height: 1.4;
            }

            .error {
                margin-top: 7px;
                color: var(--danger);
                background: #fff1f0;
            }

            .status {
                margin-bottom: 16px;
                color: var(--ok);
                background: #ecfdf3;
            }

            .password-note {
                margin-top: 8px;
                color: var(--muted);
                font-size: 13px;
                line-height: 1.45;
            }

            .password-requirements {
                margin: 10px 0 0;
                padding: 0;
                list-style: none;
                color: var(--muted);
                font-size: 13px;
                line-height: 1.6;
            }

            .password-requirements li::before {
                content: "x";
                display: inline-flex;
                width: 16px;
                height: 16px;
                margin-right: 7px;
                border-radius: 50%;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                background: var(--muted);
                font-size: 10px;
                font-weight: 800;
                vertical-align: 1px;
            }

            .password-requirements li.is-met {
                color: var(--ok);
            }

            .password-requirements li.is-met::before {
                content: "✓";
                background: var(--ok);
            }

            .age-output {
                margin-top: 8px;
                color: var(--muted);
                font-size: 13px;
                line-height: 1.45;
            }

            .hidden {
                display: none;
            }

            @media (max-width: 860px) {
                .shell {
                    padding: 18px;
                }
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <section class="form-panel">
                @yield('content')
            </section>
        </main>
    </body>
</html>
