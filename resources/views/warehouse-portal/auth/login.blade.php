<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Area Office / LSP Login</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .btn-loading-spinner {
            display: none;
            width: 1rem;
            height: 1rem;
            margin-right: .5rem;
            vertical-align: -0.125em;
            border: .18em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: btn-spinner .75s linear infinite;
        }

        .is-loading .btn-loading-spinner {
            display: inline-block;
        }

        .password-field {
            position: relative;
        }

        .password-field .form-control {
            padding-right: 2.75rem;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: .75rem;
            display: inline-flex;
            padding: .25rem;
            color: #6c757d;
            background: transparent;
            border: 0;
            transform: translateY(-50%);
        }

        .password-toggle:hover,
        .password-toggle:focus {
            color: #343a40;
            outline: none;
        }

        .password-toggle svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .password-toggle .eye-off-icon,
        .password-toggle.is-visible .eye-icon {
            display: none;
        }

        .password-toggle.is-visible .eye-off-icon {
            display: block;
        }

        @keyframes btn-spinner {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-3 text-center">Area Office Login</h4>
                        @if ($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('warehouse.login.store') }}">
                            @csrf
                            <div class="form-group">
                                <label>Email or Username</label>
                                <input type="text" name="email"  class="form-control" value="{{ old('email') }}" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="warehouse-password">Password</label>
                                <div class="password-field">
                                    <input type="password" id="warehouse-password" name="password" class="form-control" required>
                                    <button type="button" class="password-toggle" aria-label="Show password" aria-pressed="false">
                                        <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                                            <circle cx="12" cy="12" r="2.5" />
                                        </svg>
                                        <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.6 6.2A10.6 10.6 0 0 1 12 6c6 0 9.5 6 9.5 6a15.5 15.5 0 0 1-2.1 2.8M6.2 6.2C3.8 8 2.5 12 2.5 12s3.5 6 9.5 6a9.8 9.8 0 0 0 3.2-.5M9.9 9.9a3 3 0 0 0 4.2 4.2" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label for="remember" class="form-check-label">Remember me</label>
                            </div>
                            <button class="btn btn-secondary btn-block login-submit-btn" type="submit">
                                <span class="btn-loading-spinner" aria-hidden="true"></span>
                                <span class="login-button-text">Login</span>
                            </button>
                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}">Back to regular login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const passwordInput = document.getElementById('warehouse-password');
        const passwordToggle = document.querySelector('.password-toggle');

        passwordToggle?.addEventListener('click', function () {
            const isVisible = passwordInput.type === 'text';

            passwordInput.type = isVisible ? 'password' : 'text';
            passwordToggle.classList.toggle('is-visible', !isVisible);
            passwordToggle.setAttribute('aria-pressed', String(!isVisible));
            passwordToggle.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
        });

        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', function () {
                const button = form.querySelector('.login-submit-btn');
                if (!button || button.dataset.loading === 'true') {
                    return;
                }

                button.dataset.loading = 'true';
                button.classList.add('is-loading');
                button.disabled = true;

                const label = button.querySelector('.login-button-text');
                if (label) {
                    label.textContent = 'Logging in...';
                }
            });
        });
    </script>
</body>
</html>
