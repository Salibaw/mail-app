<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Dashboard - Sistem Surat Menyurat Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
            overflow-y: auto;
            z-index: 1001;
            transition: margin-left 0.3s;
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
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
            }
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #fff;
        }

        .dropdown-menu {
            z-index: 1100;
        }

        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</head>

<body>
    <div id="sidebar">
        <h4 class="text-white text-center mb-4">Staff Akademik Panel</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a href="{{ route('staff.dashboard') }}" class="nav-link {{ Request::routeIs('staff.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('staff.surat-masuk.index') }}" class="nav-link {{ Request::routeIs('staff.surat-masuk.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-envelope-open me-2"></i> Surat Masuk
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('staff.surat-keluar.index') }}" class="nav-link {{ Request::routeIs('staff.surat-keluar.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-envelope-open-text me-2"></i> Surat Keluar
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('staff.disposisi.sent.index') }}" class="nav-link {{ Request::routeIs('staff.disposisi.sent.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-paper-plane me-2"></i> Disposisi Terkirim
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('staff.disposisi.received.index') }}" class="nav-link {{ Request::routeIs('staff.disposisi.received.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-inbox me-2"></i> Disposisi Diterima
                </a>
            </li>
        </ul>
        <hr>
    </div>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded">
            <div class="container-fluid">
                <button class="btn btn-primary d-md-none me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#">Sistem Surat Menyurat</a>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{route('profile.edit')}}">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
