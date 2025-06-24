<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISUKAT - Sistem Surat Menyurat Kampus Terpadu</title>
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
            color: var(--text-color);
            background-color: var(--light-bg);
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0d2456 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            width: 150%;
            height: 150%;
            top: -50%;
            left: -25%;
            background: url('{{ asset("img/pattern.svg") }}') repeat;
            opacity: 0.05;
            animation: move 30s linear infinite;
        }

        @keyframes move {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-50px);
            }
        }

        .hero-heading {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .hero-subheading {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 50px;
        }

        .btn-primary:hover {
            background-color: #e5a400;
            border-color: #e5a400;
        }

        .btn-outline-light {
            border-radius: 50px;
            padding: 10px 24px;
            font-weight: 600;
        }

        .features-section {
            padding: 80px 0;
        }

        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(26, 60, 143, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .how-it-works {
            background-color: #fff;
            padding: 80px 0;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 50px;
            text-align: center;
            color: var(--primary-color);
        }

        .step-card {
            text-align: center;
            padding: 20px;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-weight: 600;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0d2456 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta-heading {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        footer {
            background-color: #0d2456;
            color: rgba(255, 255, 255, 0.7);
            padding: 50px 0 20px;
        }

        .footer-logo {
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
        }

        .footer-links h5 {
            color: white;
            font-size: 1.1rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .footer-links ul {
            padding: 0;
            list-style: none;
        }

        .footer-links ul li {
            margin-bottom: 10px;
        }

        .footer-links ul li a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links ul li a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            font-size: 0.9rem;
        }

        .social-links a {
            color: rgba(255, 255, 255, 0.7);
            margin: 0 10px;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-envelope-open-text me-2"></i>
                SISUKAT
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-primary" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> Masuk
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-heading">Sistem Surat Menyurat Kampus Terpadu</h1>
                    <p class="hero-subheading">Solusi digital untuk pengelolaan surat masuk, surat keluar, disposisi, dan arsip surat di lingkungan kampus secara efisien dan terstruktur.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-1"></i> Masuk Sekarang
                        </a>
                        <a href="#fitur" class="btn btn-outline-light">
                            <i class="fas fa-info-circle me-1"></i> Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="{{ asset('OIP.jpg') }}" alt="SISUKAT Illustration" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="fitur">
        <div class="container">
            <h2 class="section-title">Fitur Utama</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-envelope fa-lg"></i>
                        </div>
                        <h3 class="feature-title">Pengelolaan Surat Masuk</h3>
                        <p>Kelola semua surat masuk dengan mudah, termasuk pencatatan, pemindaian, dan pelacakan status surat.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-paper-plane fa-lg"></i>
                        </div>
                        <h3 class="feature-title">Pengelolaan Surat Keluar</h3>
                        <p>Buat dan kelola surat keluar dengan template yang dapat disesuaikan, nomor surat otomatis, dan pelacakan pengiriman.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-share fa-lg"></i>
                        </div>
                        <h3 class="feature-title">Disposisi Digital</h3>
                        <p>Lakukan disposisi surat secara digital kepada unit atau individu terkait dengan notifikasi otomatis.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-archive fa-lg"></i>
                        </div>
                        <h3 class="feature-title">Arsip Digital</h3>
                        <p>Simpan dan kelola arsip surat secara digital dengan sistem pencarian yang cepat dan mudah.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <h3 class="feature-title">Laporan & Statistik</h3>
                        <p>Dapatkan laporan dan statistik tentang surat masuk, surat keluar, dan disposisi secara real-time.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt fa-lg"></i>
                        </div>
                        <h3 class="feature-title">Akses Multi-Platform</h3>
                        <p>Akses sistem dari berbagai perangkat, termasuk desktop, tablet, dan smartphone dengan tampilan yang responsif.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works" id="cara-kerja">
        <div class="container">
            <h2 class="section-title">Cara Kerja SISUKAT</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Digitalisasi Surat</h4>
                        <p>Surat masuk dipindai dan diinput ke dalam sistem dengan metadata lengkap.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Distribusi & Disposisi</h4>
                        <p>Surat didistribusikan dan didisposisikan secara digital ke pihak terkait.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Tindak Lanjut</h4>
                        <p>Penerima disposisi melakukan tindak lanjut dan memperbarui status di sistem.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h4>Arsip & Pelaporan</h4>
                        <p>Surat diarsipkan secara digital dan dapat diakses untuk pelaporan kapan saja.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-heading">Siap Menggunakan SISUKAT?</h2>
            <p class="mb-4">Tingkatkan efisiensi pengelolaan surat menyurat kampus Anda sekarang juga.</p>
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i> Masuk ke Sistem
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-logo">
                        <i class="fas fa-envelope-open-text me-2"></i> SISUKAT
                    </div>
                    <p>Sistem Surat Menyurat Kampus Terpadu untuk pengelolaan surat masuk, surat keluar, disposisi, dan arsip di lingkungan kampus secara digital.</p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <div class="footer-links">
                        <h5>Tautan Cepat</h5>
                        <ul>
                            <li><a href="#">Beranda</a></li>
                            <li><a href="#fitur">Fitur</a></li>
                            <li><a href="#cara-kerja">Cara Kerja</a></li>
                            <li><a href="{{ route('login') }}">Masuk</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <div class="footer-links">
                        <h5>Bantuan & Dukungan</h5>
                        <ul>
                            <li><a href="#">Panduan Pengguna</a></li>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Pusat Bantuan</a></li>
                            <li><a href="#">Hubungi Support</a></li>
                            <li><a href="#">Kebijakan Privasi</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <div class="footer-links">
                        <h5>Kontak Kami</h5>
                        <ul>
                            <li><i class="fas fa-map-marker-alt me-2"></i> Jl. Raya Lenteng, Aredake, Batuan, Kec. Batuan, Kabupaten Sumenep, Jawa Timur 69451</li>
                            <li><i class="fas fa-phone me-2"></i> (0328) 6771010</li>
                            <li><i class="fas fa-envelope me-2"></i> info@sisukat.ac.id</li>
                            <li><i class="fas fa-clock me-2"></i> Senin - Sabtu, 08:00 - 16:00</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 SISUKAT - Sistem Surat Menyurat Kampus Terpadu. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>