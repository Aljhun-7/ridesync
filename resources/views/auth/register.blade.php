@extends('layouts.auth', [
    'title' => 'Register | RideSync',
    'headline' => 'Create access.',
    'copy' => 'Register as a customer. Admin access is limited to one account.',
])

@section('content')
    <div class="form-card">
        @if (file_exists(public_path('images/logo.png')))
            <img class="auth-logo" src="{{ asset('images/logo.png') }}" alt="RideSync logo">
        @endif
        <h2>Registration</h2>
        <p class="subtitle"></p>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <label for="name">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="role">Account type</label>
            <select id="role" name="role" required>
                <option value="customer" @selected(old('role', 'customer') === 'customer')>Customer</option>
                <option value="admin" @selected(old('role') === 'admin') @disabled($adminExists)>Admin{{ $adminExists ? ' - already registered' : '' }}</option>
            </select>
            @error('role')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="birthday">Birthday</label>
            <input id="birthday" name="birthday" type="date" value="{{ old('birthday') }}" max="{{ now()->toDateString() }}" required>
            <div id="age-output" class="age-output">Age will be calculated after you choose your birthday.</div>
            @error('birthday')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <div class="password-field">
                <input id="password" name="password" type="password" minlength="8" maxlength="24" autocomplete="new-password" required>
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
            <ul class="password-requirements" aria-live="polite">
                <li data-password-rule="length">8 to 24 characters</li>
                <li data-password-rule="letter">At least one letter</li>
                <li data-password-rule="number">At least one number</li>
                <li data-password-rule="symbol">At least one special character</li>
            </ul>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">Re-type password</label>
            <div class="password-field">
                <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" maxlength="24" autocomplete="new-password" required>
                <button class="password-toggle" type="button" data-toggle-password="password_confirmation" aria-label="Show re-type password" aria-pressed="false">
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
            <div id="match-message" class="password-note"></div>

            <button class="button" type="submit">Register</button>
        </form>

        <p class="helper">Already registered? <a href="{{ route('login') }}">Go to login</a></p>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');
        const matchMessage = document.getElementById('match-message');
        const birthday = document.getElementById('birthday');
        const ageOutput = document.getElementById('age-output');
        const passwordRules = {
            length: (value) => value.length >= 8 && value.length <= 24,
            letter: (value) => /[A-Za-z]/.test(value),
            number: (value) => /\d/.test(value),
            symbol: (value) => /[^A-Za-z0-9]/.test(value),
        };

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

        function calculateAge(dateValue) {
            if (!dateValue) {
                return null;
            }

            const birthDate = new Date(`${dateValue}T00:00:00`);
            const today = new Date();

            if (Number.isNaN(birthDate.getTime()) || birthDate > today) {
                return null;
            }

            let age = today.getFullYear() - birthDate.getFullYear();
            const birthdayPassed = today.getMonth() > birthDate.getMonth()
                || (today.getMonth() === birthDate.getMonth() && today.getDate() >= birthDate.getDate());

            if (!birthdayPassed) {
                age -= 1;
            }

            return age;
        }

        function updateAge() {
            const age = calculateAge(birthday.value);

            if (age === null) {
                ageOutput.textContent = 'Age will be calculated after you choose your birthday.';
                ageOutput.style.color = 'var(--muted)';
                birthday.setCustomValidity('');
                return;
            }

            ageOutput.textContent = `Age: ${age}`;
            ageOutput.style.color = 'var(--ok)';
            birthday.setCustomValidity('');
        }

        function updatePasswordRequirements() {
            let allRulesMet = true;

            document.querySelectorAll('[data-password-rule]').forEach((item) => {
                const rule = item.dataset.passwordRule;
                const isMet = passwordRules[rule](password.value);

                item.classList.toggle('is-met', isMet);
                allRulesMet = allRulesMet && isMet;
            });

            password.setCustomValidity(password.value && !allRulesMet ? 'Please meet all password requirements.' : '');
        }

        function updatePasswordMatch() {
            if (!confirmation.value) {
                matchMessage.textContent = '';
                confirmation.setCustomValidity('');
                return;
            }

            if (password.value === confirmation.value) {
                matchMessage.textContent = 'Passwords match.';
                matchMessage.style.color = 'var(--ok)';
                confirmation.setCustomValidity('');
                return;
            }

            matchMessage.textContent = 'Passwords do not match.';
            matchMessage.style.color = 'var(--danger)';
            confirmation.setCustomValidity('Passwords do not match.');
        }

        birthday.addEventListener('input', updateAge);
        password.addEventListener('input', () => {
            updatePasswordRequirements();
            updatePasswordMatch();
        });
        confirmation.addEventListener('input', updatePasswordMatch);
        updateAge();
        updatePasswordRequirements();
    </script>
@endsection
