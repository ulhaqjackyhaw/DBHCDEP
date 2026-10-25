<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logo-title.png') }}">
    <title>Login - Analitik Kepegawaian</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --text-light: #1e293b;
            --text-dark: #f8fafc;
            --border-light: #e2e8f0;
            --border-dark: #334155;
            --input-light: #f8fafc;
            --input-dark: #1e293b;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: url('{{ asset('images/logo/T3bg.jpg') }}') center/cover no-repeat fixed;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(67, 56, 202, 0.12));
            z-index: 0;
        }

        html.dark body::before {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.9));
        }

        .wrapper {
            width: 100%;
            max-width: 1050px;
            border-radius: 1.25rem;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
            background: #ffffff;
        }

        html.dark .wrapper {
            background: rgba(30, 41, 59, 0.98);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .form-section {
            padding: clamp(2rem, 5vw, 3.5rem);
            text-align: center;
            background: #ffffff;
        }

        html.dark .form-section {
            background: rgba(30, 41, 59, 0.98);
        }

        .theme-toggle {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .3s ease;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: none;
        }

        html.dark .theme-toggle {
            background: rgba(30, 41, 59, 0.95);
            color: #f8fafc;
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
            background: var(--primary);
            color: #fff;
        }

        .theme-toggle .fa-sun {
            display: none;
        }

        .theme-toggle .fa-moon {
            display: block;
        }

        html.dark .theme-toggle .fa-sun {
            display: block;
        }

        html.dark .theme-toggle .fa-moon {
            display: none;
        }

        .form-section img.logo {
            max-width: 120px;
            margin: 1rem auto 1.5rem;
            display: block;
        }

        .form-section h1 {
            font-weight: 700;
            font-size: 1.75rem;
            margin: .3rem 0 .6rem;
            color: var(--text-light);
        }

        html.dark .form-section h1 {
            color: var(--text-dark);
        }

        .form-section .subtitle {
            color: #64748b;
        }

        html.dark .form-section .subtitle {
            color: #94a3b8;
        }

        .alert {
            margin-bottom: 1.5rem;
            border-radius: 0.75rem;
            padding: 0;
            overflow: hidden;
            animation: slideDown 0.3s ease-out;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: 1px solid #fca5a5;
        }

        html.dark .alert-error {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.15), rgba(239, 68, 68, 0.15));
            border-color: rgba(220, 38, 38, 0.3);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #fbbf24;
        }

        html.dark .alert-warning {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(251, 191, 36, 0.15));
            border-color: rgba(245, 158, 11, 0.3);
        }

        .alert-content {
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            gap: 0.75rem;
        }

        .alert-content i {
            color: #dc2626;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        html.dark .alert-content i {
            color: #f87171;
        }

        .alert-text {
            flex: 1;
            color: #7f1d1d;
            font-weight: 500;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        html.dark .alert-text {
            color: #fca5a5;
        }

        .alert-close {
            background: none;
            border: none;
            color: #dc2626;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .alert-close:hover {
            background: rgba(220, 38, 38, 0.1);
            transform: scale(1.1);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert.fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
                max-height: 100px;
            }

            to {
                opacity: 0;
                transform: translateY(-10px);
                max-height: 0;
            }
        }

        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: .875rem;
            font-weight: 500;
            margin-bottom: .4rem;
        }

        html.dark .form-group label {
            color: #f8fafc;
        }

        .form-group input {
            width: 100%;
            padding: .85rem 1rem;
            border-radius: .5rem;
            border: 1px solid var(--border-light);
            background: var(--input-light);
            color: var(--text-light);
            font-size: 1rem;
            font-family: inherit;
            transition: all .2s;
        }

        html.dark .form-group input {
            border-color: var(--border-dark);
            background: var(--input-dark);
            color: var(--text-dark);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper input {
            padding-right: 3rem;
        }

        .password-wrapper .toggle-password {
            position: absolute;
            right: 1rem;
            cursor: pointer;
            color: #94a3b8;
            transition: color .2s;
        }

        .password-wrapper .toggle-password:hover {
            color: var(--primary);
        }

        .field-error {
            display: block;
            color: #dc2626;
            font-size: 0.8rem;
            margin-top: 0.4rem;
            font-weight: 500;
        }

        html.dark .field-error {
            color: #f87171;
        }

        .form-group input.error {
            border-color: #dc2626 !important;
            background: rgba(220, 38, 38, 0.05);
        }

        .form-extras {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .875rem;
            margin-top: .8rem;
        }

        .form-extras .checkbox-group {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        html.dark .form-extras .checkbox-group label {
            color: #f8fafc;
        }

        .form-extras a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .form-extras a:hover {
            text-decoration: underline;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            width: 100%;
            padding: 1rem;
            border-radius: .6rem;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1.5rem;
            transition: all .2s;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .separator {
            position: relative;
            margin: 1.5rem 0;
            text-align: center;
            color: #94a3b8;
            font-size: .875rem;
        }

        .separator::before,
        .separator::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: var(--border-light);
        }

        html.dark .separator::before,
        html.dark .separator::after {
            background: var(--border-dark);
        }

        .separator::before {
            left: 0;
        }

        .separator::after {
            right: 0;
        }

        .social-login .btn {
            background: var(--input-light);
            border: 1px solid var(--border-light);
            color: var(--text-light);
        }

        html.dark .social-login .btn {
            background: var(--input-dark);
            border-color: var(--border-dark);
            color: var(--text-dark);
        }

        .social-login .btn:hover {
            background: #f1f5f9;
        }

        html.dark .social-login .btn:hover {
            background: #334155;
        }

        .social-login .btn i {
            font-size: 1.2rem;
        }

        .illustration-section {
            background: linear-gradient(135deg, var(--primary), #4338ca);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            padding: clamp(16px, 4vw, 40px);
            min-height: clamp(380px, 62vh, 640px);
            color: #fff;
        }

        .illustration-section::before,
        .illustration-section::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .illustration-section::before {
            width: 300px;
            height: 300px;
            bottom: -150px;
            left: -150px;
        }

        .illustration-section::after {
            width: 200px;
            height: 200px;
            top: -100px;
            right: -100px;
        }

        .slider-wrapper {
            width: 100%;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .slider {
            display: flex;
            width: 300%;
            animation: slide 15s infinite;
        }

        .slide {
            flex: 0 0 33.3333%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide img {
            width: auto;
            height: clamp(220px, 40vh, 360px);
            max-width: 90%;
            object-fit: contain;
        }

        .slide-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 100%;
            padding: 0 1.5rem;
        }

        .slide-content h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .slide-content p {
            font-size: .95rem;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.85);
            max-width: 90%;
        }

        .slider-pagination {
            display: flex;
            gap: .5rem;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: .3s;
            animation: dot-anim 15s infinite;
        }

        .dot:nth-child(1) {
            animation-delay: 0s;
        }

        .dot:nth-child(2) {
            animation-delay: -10s;
        }

        .dot:nth-child(3) {
            animation-delay: -5s;
        }

        @keyframes slide {

            0%,
            28% {
                transform: translateX(0%);
            }

            33.33%,
            61.33% {
                transform: translateX(-33.3333%);
            }

            66.66%,
            94.66% {
                transform: translateX(-66.6666%);
            }

            100% {
                transform: translateX(0);
            }
        }

        @keyframes dot-anim {

            0%,
            33.33% {
                background: rgba(255, 255, 255, 0.9);
                transform: scale(1.2);
            }

            33.34%,
            100% {
                background: rgba(255, 255, 255, 0.4);
                transform: scale(1);
            }
        }

        @media(max-width:992px) {
            body {
                align-items: flex-start;
                padding-top: 4rem;
            }

            .wrapper {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .illustration-section {
                display: none;
            }
        }
    </style>

    <script>
        (function() {
            const htmlEl = document.documentElement;
            const storedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (storedTheme === 'dark' || (!storedTheme && systemPrefersDark)) {
                htmlEl.classList.add('dark');
            } else {
                htmlEl.classList.remove('dark');
            }
        })();
    </script>
</head>

<body>
    <button class="theme-toggle" id="theme-toggle" title="Ganti tema">
        <i class="fas fa-moon"></i>
        <i class="fas fa-sun"></i>
    </button>

    <div class="wrapper">
        <div class="form-section">
            <img src="images/logo/company-logo.png" alt="Logo Injourney Airports" class="logo" />
            <h1>Analitik Kepegawaian</h1>
            <p class="subtitle">Silakan login untuk melanjutkan</p>

            @if ($errors->any() || session('error') || session('session_expired'))
                <div class="alert {{ session('session_expired') ? 'alert-warning' : 'alert-error' }}" id="error-alert">
                    <div class="alert-content">
                        <i class="fas {{ session('session_expired') ? 'fa-clock' : 'fa-exclamation-triangle' }}"></i>
                        <div class="alert-text">
                            @if (session('error'))
                                {{ session('error') }}
                            @else
                                @foreach ($errors->all() as $error)
                                    {{ $error }}
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="alert-close" onclick="closeAlert()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="@error('email') error @enderror" />
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required
                            class="@error('password') error @enderror" />
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-extras">
                    <div class="checkbox-group">
                        <input type="checkbox" id="remember-me" name="remember-me">
                        <label for="remember-me">Ingat Saya</label>
                    </div>
                    <a href="#">Lupa Password?</a>
                </div>
                <button type="submit" class="btn btn-primary" id="login-btn">
                    <span class="btn-text">Login</span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        Memproses...
                    </span>
                </button>
            </form>
            <div class="separator">atau</div>
            <div class="social-login">
                <button class="btn">
                    <i class="fab fa-google"></i><span>Login dengan Google</span>
                </button>
            </div>
        </div>

        <div class="illustration-section">
            <div class="slider-wrapper">
                <div class="slider">
                    <div class="slide">
                        <div class="slide-content">
                            <img src="{{ asset('images/illustrations/1.svg') }}" alt="Analytics" />
                            <h2>Dashboard Kepegawaian</h2>
                            <p>Dapatkan insight mendalam tentang kepegawaian Anda.</p>
                        </div>
                    </div>
                    <div class="slide">
                        <div class="slide-content">
                            <img src="{{ asset('images/illustrations/2.svg') }}" alt="Authentication" />
                            <h2>Keamanan Data Terjamin</h2>
                            <p>Sistem kami menjaga informasi penting Anda tetap aman dan rahasia.</p>
                        </div>
                    </div>
                    <div class="slide">
                        <div class="slide-content">
                            <img src="{{ asset('images/illustrations/3.svg') }}" alt="Growth" />
                            <h2>Optimalkan Pertumbuhan</h2>
                            <p>Pantau tren dan kembangkan potensi karyawan Anda secara maksimal.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-pagination">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const htmlEl = document.documentElement;

            themeToggle.addEventListener('click', () => {
                htmlEl.classList.toggle('dark');
                if (htmlEl.classList.contains('dark')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
            });

            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye-slash');
                });
            }

            const errorAlert = document.getElementById('error-alert');
            if (errorAlert) {
                setTimeout(() => {
                    closeAlert();
                }, 5000);
            }

            const loginForm = document.querySelector('form');
            const loginBtn = document.getElementById('login-btn');
            const btnText = loginBtn.querySelector('.btn-text');
            const btnLoading = loginBtn.querySelector('.btn-loading');

            loginForm.addEventListener('submit', function(e) {
                btnText.style.display = 'none';
                btnLoading.style.display = 'flex';
                loginBtn.disabled = true;

                setTimeout(() => {
                    btnText.style.display = 'flex';
                    btnLoading.style.display = 'none';
                    loginBtn.disabled = false;
                }, 10000);
            });
        });

        function closeAlert() {
            const alert = document.getElementById('error-alert');
            if (alert) {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            }
        }
    </script>
</body>

</html>
