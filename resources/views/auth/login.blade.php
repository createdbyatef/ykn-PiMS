<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#e4d7f0">
    <title>{{ __('Log Masuk') }} — e-Pilot YKN</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/epilot.css') }}?v=14" rel="stylesheet">
    @include('partials.splash-head', ['skipSplash' => $errors->any()])
    <style>
        body {
            min-height: 100vh;
            background: radial-gradient(circle at 75% 48%, #e5d8f2 0%, #e3d6ef 55%, #ded0ec 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow-x: hidden;
        }
        .login-stage {
            width: 100%;
            max-width: 1020px;
            display: grid;
            grid-template-columns: 1fr;
            align-items: center;
            gap: 2rem;
            position: relative;
        }
        @media (min-width: 992px) {
            .login-stage {
                grid-template-columns: 430px 1fr;
                gap: 1.5rem;
            }
        }
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 2.25rem 2.25rem 2rem;
            box-shadow: 0 24px 60px rgba(68, 52, 114, 0.14), 0 4px 16px rgba(68, 52, 114, 0.06);
            position: relative;
            z-index: 2;
        }
        @media (max-width: 575.98px) {
            .login-card {
                padding: 1.75rem 1.4rem;
                border-radius: 20px;
            }
        }
        .login-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }
        .login-brand img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }
        .login-brand-name {
            font-size: 0.92rem;
            font-weight: 700;
            color: #1f1d2b;
            letter-spacing: -0.015em;
        }
        .login-brand-name span {
            color: var(--ykn-orchid);
        }
        .login-meta-right {
            text-align: right;
            font-size: 0.72rem;
            line-height: 1.35;
            color: var(--ykn-muted);
        }
        .login-meta-right strong {
            display: block;
            color: var(--ykn-violet-700);
            font-weight: 600;
        }
        .login-heading {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f1d2b;
            letter-spacing: -0.025em;
            margin: 1.35rem 0 0.25rem;
        }
        .login-subheading {
            font-size: 0.8rem;
            color: var(--ykn-muted);
            margin-bottom: 1.5rem;
        }
        .field-label {
            font-size: 0.76rem;
            font-weight: 500;
            color: #5b5770;
            margin-bottom: 0.35rem;
        }
        .login-input {
            height: 44px;
            border-radius: 10px;
            border: 1.5px solid #e2dce9;
            padding: 0.55rem 0.9rem;
            font-size: 0.84rem;
            color: #1f1d2b;
            background: #ffffff;
            transition: border-color 0.16s ease, box-shadow 0.16s ease;
        }
        .login-input:focus {
            border-color: var(--ykn-orchid);
            box-shadow: 0 0 0 3.5px rgba(168, 85, 214, 0.14);
        }
        .password-wrap {
            position: relative;
        }
        .password-wrap .login-input {
            padding-right: 2.6rem;
        }
        .password-toggle {
            position: absolute;
            right: 0.65rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #8f8aa6;
            padding: 0.25rem 0.35rem;
            line-height: 1;
            cursor: pointer;
        }
        .password-toggle:hover {
            color: var(--ykn-orchid-600);
        }
        .btn-signin {
            background: #2b2738;
            border: 1px solid #2b2738;
            color: #ffffff;
            border-radius: 10px;
            padding: 0.62rem 1.75rem;
            font-size: 0.84rem;
            font-weight: 600;
            transition: background-color 0.18s ease, border-color 0.18s ease, transform 0.12s ease, box-shadow 0.18s ease;
        }
        .btn-signin:hover,
        .btn-signin:focus {
            background: var(--ykn-orchid);
            border-color: var(--ykn-orchid);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(168, 85, 214, 0.28);
        }
        .btn-signin:active {
            transform: translateY(1px);
        }
        .helper-link {
            font-size: 0.74rem;
            font-weight: 500;
            color: var(--ykn-violet-700);
            text-decoration: none;
        }
        .helper-link:hover {
            color: var(--ykn-orchid-600);
            text-decoration: underline;
        }
        .demo-strip {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #f1eff6;
        }
        .demo-chip {
            border: 1px solid #e5dfef;
            background: #faf8fd;
            color: #4b475f;
            border-radius: 8px;
            padding: 0.36rem 0.65rem;
            font-size: 0.74rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }
        .demo-chip:hover {
            border-color: #d4aeeF;
            background: var(--ykn-orchid-50);
            color: var(--ykn-orchid-700);
        }
        .illustration-pane {
            display: none;
            position: relative;
            text-align: center;
        }
        @media (min-width: 992px) {
            .illustration-pane {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
        }
        .illustration-img {
            width: 100%;
            max-width: 520px;
            height: auto;
            display: block;
            margin: 0 auto;
            -webkit-mask-image: radial-gradient(ellipse 76% 76% at 50% 50%, #000 66%, transparent 100%);
            mask-image: radial-gradient(ellipse 76% 76% at 50% 50%, #000 66%, transparent 100%);
            user-select: none;
            pointer-events: none;
        }
        .illustration-caption {
            margin-top: 0.5rem;
            position: relative;
            z-index: 2;
            font-size: 0.76rem;
            color: #5b5276;
            font-weight: 500;
            letter-spacing: 0.01em;
        }
    </style>
</head>
<body>
    @include('partials.splash')
    <main class="login-stage">
        {{-- Left: Crisp Elevated White Login Card --}}
        <section class="login-card">
            <div class="d-flex justify-content-between align-items-center gap-2">
                <a href="{{ route('login') }}" class="login-brand">
                    <img src="{{ asset('images/ykn-emblem.png') }}" alt="Logo YKN">
                    <span class="login-brand-name">e-Pilot <span>YKN</span></span>
                </a>
                <div class="d-flex align-items-center gap-2">
                    <div class="lang-switch-pill" role="group" aria-label="Language switch">
                        <a href="{{ route('locale.switch', 'ms') }}" class="lang-switch-opt {{ app()->getLocale() === 'ms' ? 'active' : '' }}">BM</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="lang-switch-opt {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    </div>
                </div>
            </div>

            <h1 class="login-heading">{{ __('Log Masuk') }}</h1>
            <p class="login-subheading">{{ __('Sistem Pengurusan Tempahan Kenderaan & Pilot HQ, Yayasan Kebajikan Negara Malaysia.') }}</p>

            @if(session('success'))
                <div class="alert alert-success py-2 mb-3 border-0">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 mb-3 border-0 d-flex gap-2 align-items-start">
                    <i class="bi bi-exclamation-circle-fill mt-1"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label for="email" class="field-label d-block">{{ __('Alamat Emel Rasmi') }}</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control login-input @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="nama@ykn.gov.my"
                        required
                        autofocus
                        autocomplete="username"
                    >
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="field-label mb-0">{{ __('Kata Laluan') }}</label>
                        <span class="helper-link" title="{{ __('Hubungi Unit Pentadbiran YKN untuk menetapkan semula kata laluan') }}">{{ __('Lupa kata laluan?') }}</span>
                    </div>
                    <div class="password-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control login-input @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="{{ __('Papar kata laluan') }}">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label text-muted" style="font-size: 0.78rem;" for="remember">{{ __('Ingat sesi saya') }}</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-signin">
                    {{ __('Log Masuk') }}
                </button>
            </form>

            @if(app()->environment('local'))
                <div class="demo-strip">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="section-label">{{ __('Akaun Demo Pantas') }}</span>
                        <span class="text-muted" style="font-size: 0.7rem;">{{ __('Kata laluan:') }} <code>password</code></span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="demo-chip" onclick="fillDemo('admin@ykn.gov.my')">
                            <i class="bi bi-shield-check me-1 text-orchid"></i>{{ __('Admin') }}
                        </button>
                        <button type="button" class="demo-chip" onclick="fillDemo('huda@ykn.gov.my')">
                            <i class="bi bi-person me-1 text-violet"></i>{{ __('Staf YKN') }}
                        </button>
                        <button type="button" class="demo-chip" onclick="fillDemo('roslan.pilot@ykn.gov.my')">
                            <i class="bi bi-person-badge me-1 text-brand-orange"></i>{{ __('Pilot HQ') }}
                        </button>
                    </div>
                </div>
            @endif
        </section>

        {{-- Right: Seamless 3D Clay Illustration --}}
        <section class="illustration-pane" aria-hidden="true">
            <img src="{{ asset('images/login-illustration.jpg') }}" alt="Ilustrasi e-Pilot YKN" class="illustration-img">
            <div class="illustration-caption">{!! __('Yayasan Kebajikan Negara Malaysia &middot; Kenderaan &amp; Pilot HQ') !!}</div>
        </section>
    </main>

    <script>
        function fillDemo(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
            document.getElementById('email').focus();
        }
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = this.querySelector('.btn-signin');
            if (btn) btn.disabled = true;
            if (typeof window.yknShowSplash === 'function') {
                window.yknShowSplash(@json(__('Mengesahkan akses anda...')));
            }
        });
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                const btn = document.querySelector('#loginForm .btn-signin');
                if (btn) btn.disabled = false;
            }
        });
    </script>
</body>
</html>
