<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SISUKAT</title>
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
            0% { transform: translateY(0); }
            100% { transform: translateY(-50px); }
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

        .login-footer {
            margin-top: 20px;
            text-align: center;
            color: #6c757d;
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
        }

        @media (max-width: 767.98px) {
            .login-banner {
                padding: 30px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="row g-0 login-card">
            <!-- Banner -->
            <div class="col-md-6 d-none d-md-block">
                <div class="login-banner">
                    <div class="login-banner-content">
                        <div class="login-logo"><i class="fas fa-envelope-open-text me-2"></i> SISUKAT</div>
                        <div class="login-headline">Bergabung di Sistem Surat Kampus</div>
                        <p>Kelola surat masuk, keluar, dan disposisi dengan mudah!</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="col-md-6">
                <div class="login-form-container">
                    <div class="login-form-title">Buat Akun</div>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" required>
                            <label for="nama"><i class="fas fa-user me-1"></i> Nama Lengkap</label>
                        </div>

                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" required>
                            <label for="email"><i class="fas fa-envelope me-1"></i> Email</label>
                        </div>

                        <div class="form-floating position-relative">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password"><i class="fas fa-lock me-1"></i> Kata Sandi</label>
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-3" onclick="togglePassword()" style="cursor: pointer;">
                                <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                            </span>
                        </div>

                        <div class="form-floating">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password" required>
                            <label for="password_confirmation"><i class="fas fa-lock me-1"></i> Konfirmasi Sandi</label>
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-3" onclick="togglePassword()" style="cursor: pointer;">
                                <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                            </span>
                        </div>

                        <button type="submit" class="btn btn-login mt-3">Daftar</button>

                        <div class="login-footer mt-4">
                            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Password toggle script -->
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
