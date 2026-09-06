@extends('layouts.auth', [
    'title' => 'Login | RideSync',
    'headline' => 'Welcome back.',
    'copy' => 'Sign in to continue to the right RideSync workspace for your account.',
])

@section('content')
    <div class="form-card">
        @if (file_exists(public_path('images/logo.png')))
            <img class="auth-logo" src="{{ asset('images/logo.png') }}" alt="RideSync logo">
        @endif
        <h2>Login</h2>
        <p class="subtitle">Use your registered email and password.</p>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <div class="password-field">
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                <button class="password-toggle" type="button" data-toggle-password="password" aria-label="Show password" aria-pressed="false">
                    <svg class="eye-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-off-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 3l18 18"></path>
                        <path d="M10.6 10.6A2 2 0 0 0 12 14a2 2 0 0 0 1.4-.6"></path>
                        <path d="M9.9 4.2A10.5 10.5 0 0 1 12 4c6.5 0 10 8 10 8a16.3 16.3 0 0 1-3 4.1"></path>
                        <path d="M6.6 6.6C3.7 8.5 2 12 2 12s3.5 8 10 8a9.7 9.7 0 0 0 4-.8"></path>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label class="check-row" for="remember">
                <input id="remember" name="remember" type="checkbox" value="1">
                Remember me
            </label>

            <button class="button" type="submit">Log in</button>
        </form>

        <p class="helper">No account yet? <a href="{{ route('register') }}">Create one</a></p>
    </div>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            const input = document.getElementById(button.dataset.togglePassword);

            button.addEventListener('click', () => {
                const isVisible = input.type === 'text';
                input.type = isVisible ? 'password' : 'text';
                button.classList.toggle('is-visible', !isVisible);
                button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
                button.setAttribute('aria-pressed', String(!isVisible));
            });
        });
    </script>
@endsection
