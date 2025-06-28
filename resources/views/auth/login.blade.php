<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SISUKAT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a3c8f;
            --secondary-color: #f8b100;
            --accent-color: #e74c3c;
            --text-color: #333;
            --light-bg: #f5f8fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7ec 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 900px;
            width: 100%;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .login-banner {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0d2456 100%);
            color: white;
            padding: 40px;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-banner::before {
            content: "";
            position: absolute;
            width: 150%;
            height: 150%;
            top: -25%;
            left: -25%;
            background: url('{{ asset("img/pattern.svg") }}') repeat;
            opacity: 0.1;
            animation: moveBg 30s linear infinite;
        }

        @keyframes moveBg {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-50px);
            }
        }

        .login-banner-content {
            position: relative;
            z-index: 1;
        }

        .login-logo {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .login-headline {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .login-features {
            padding: 0;
            list-style-type: none;
            margin-top: 2rem;
        }

        .login-features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .login-features li i {
            background-color: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .login-form-container {
            padding: 40px;
        }

        .login-form-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 30px;
            color: var(--primary-color);
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating label {
            color: #6c757d;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(26, 60, 143, 0.25);
        }

        .btn-login {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            width: 100%;
            padding: 12px;
            font-weight: 600;
            border-radius: 5px;
        }

        .btn-login:hover {
            background-color: #132d6e;
            border-color: #132d6e;
        }

        .login-separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
            color: #6c757d;
        }

        .login-separator::before,
        .login-separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }

        .login-separator::before {
            margin-right: 10px;
        }

        .login-separator::after {
            margin-left: 10px;
        }

        .btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 15px;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-icon:hover {
            background-color: #f8f9fa;
        }

        .btn-icon i {
            margin-right: 10px;
        }

        .login-footer {
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 767.98px) {
            .login-banner {
                padding: 30px;
                text-align: center;
            }

            .login-features li {
                justify-content: center;
            }

            .login-form-container {
                padding: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="container login-container">
        <div class="row g-0 login-card">
            <!-- Banner Section -->
            <div class="col-md-6 d-none d-md-block">
                <div class="login-banner">
                    <div class="login-banner-content">
                        <div class="login-logo">
                            <i class="fas fa-envelope-open-text me-2"></i> SISUKAT
                        </div>
                        <div class="login-headline">
                            Selamat Datang di SISUKAT
                        </div>
                        <p>Sistem Informasi Surat Menyurat Kampus</p>
                        <ul class="login-features">
                            <li><i class="fas fa-paper-plane"></i> Kirim dan terima surat secara digital</li>
                            <li><i class="fas fa-archive"></i> Arsipkan surat dengan mudah</li>
                            <li><i class="fas fa-share"></i> Disposisi antar unit kampus</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="col-md-6">
                <div class="login-form-container">
                    <div class="login-form-title">Masuk ke Akun Anda</div>
                    @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" required>
                            <label for="email">
                                <i class="fas fa-envelope me-1"></i> Email
                            </label>
                        </div>

                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password">
                                <i class="fas fa-lock me-1"></i> Kata Sandi
                            </label>
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-3" onclick="togglePassword()" style="cursor: pointer;">
                                <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-login">Masuk</button>
                    </form>

                    <div class="login-footer">
                        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            const isHidden = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
</body>

</html>