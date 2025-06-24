<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa Dashboard - Sistem Surat Menyurat Kampus</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome (Opsional, untuk ikon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        #sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
            flex-shrink: 0;
        }

        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
        }

        #sidebar .nav-link:hover {
            color: white;
            background-color: #495057;
        }

        #sidebar .nav-link.active {
            color: white;
            background-color: #007bff;
        }

        #content {
            flex-grow: 1;
            padding: 20px;
        }

        .navbar {
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .05);
        }

        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</head>

<body>
    <div id="sidebar" class="d-flex flex-column p-3">
        <h4 class="text-white text-center mb-4">Mahasiswa Panel</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link {{ Request::routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('mahasiswa.surat-keluar.index') }}" class="nav-link {{ Request::routeIs('mahasiswa.surat-keluar.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-envelope-open-text me-2"></i> Surat Keluar Anda
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('mahasiswa.surat-masuk.index') }}" class="nav-link {{ Request::routeIs('mahasiswa.surat-masuk.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-envelope-open-text me-2"></i> Surat Masuk Anda
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('mahasiswa.disposisi.index') }}" class="nav-link {{ Request::routeIs('mahasiswa.disposisi.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-share-square me-2"></i> Disposisi Saya
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle me-2"></i>
                <strong>{{ Auth::user()->nama ?? 'Mahasiswa' }}</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="#">Profil</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Sign out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Sistem Surat Menyurat</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <span class="nav-link text-dark">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>

</html>