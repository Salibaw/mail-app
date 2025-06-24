<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Dashboard - Sistem Surat Menyurat Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
            transition: all 0.3s;
        }
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px; /* Hide sidebar by default on small screens */
                position: fixed;
                height: 100%;
                z-index: 1000; /* Ensure it's above other content */
            }
            #sidebar.active {
                margin-left: 0; /* Show sidebar when active */
            }
            #content {
                width: 100%; /* Take full width when sidebar is hidden */
            }
            .navbar-toggler {
                display: block; /* Show toggler on small screens */
            }
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
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
        }
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        /* Custom responsive table styles */
        @media (max-width: 767.98px) {
            .table-responsive-sm-cards table {
                border-collapse: separate; /* Allow border-radius on cells */
                border-spacing: 0 10px; /* Space between "cards" */
                width: 100%;
            }
            .table-responsive-sm-cards thead {
                display: none; /* Hide table headers on small screens */
            }
            .table-responsive-sm-cards tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                padding: 10px;
            }
            .table-responsive-sm-cards tbody td {
                display: block;
                text-align: left !important;
                border: none;
                padding: 5px 10px;
            }
            .table-responsive-sm-cards tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                display: inline-block;
                width: 120px; /* Adjust as needed */
            }
            .table-responsive-sm-cards tbody td:last-child {
                border-bottom: none;
            }
        }
    </style>
</head>
<body>
    <div id="sidebar" class="d-flex flex-column p-3">
        <h4 class="text-white text-center mb-4">Staff Akademik Panel</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a href="{{ route('staff.dashboard') }}" class="nav-link {{ Request::routeIs('staff.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('staff.surat-masuk.index') }}" class="nav-link {{ Request::routeIs('staff.surat-masuk.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-envelope-open me-2"></i> Manajemen Surat Masuk
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('staff.surat-keluar.index') }}" class="nav-link {{ Request::routeIs('staff.surat-keluar.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-envelope-open-text me-2"></i> Manajemen Surat Keluar
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('staff.disposisi.sent.index') }}" class="nav-link {{ Request::routeIs('staff.disposisi.sent.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-paper-plane me-2"></i> Disposisi Saya Kirim
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('staff.disposisi.received.index') }}" class="nav-link {{ Request::routeIs('staff.disposisi.received.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-inbox me-2"></i> Disposisi Saya Terima
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle me-2"></i>
                <strong>{{ Auth::user()->nama ?? 'Staf TU' }}</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="#">Profil</a></li>
                <li><hr class="dropdown-divider"></li>
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
                <button class="btn btn-primary d-md-none me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#">Sistem Surat Menyurat</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#sidebarToggle').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
    @stack('modals')
    @stack('scripts')
</body>
</html>