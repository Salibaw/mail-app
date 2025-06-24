<header class="bg-white shadow p-4 flex justify-between items-center">
    <!-- Toggle Sidebar Button (Mobile) -->
    <button onclick="toggleSidebar()" class="lg:hidden md:hidden text-indigo-800">
        <i class="fas fa-bars text-2xl"></i>
    </button>

    <!-- Judul Halaman -->
    <h1 class="text-xl font-semibold text-gray-800">
        @yield('page-title', 'Dashboard')
    </h1>

    <!-- User Profile Dropdown -->
    <div class="relative">
        <button onclick="toggleProfileDropdown()" class="flex items-center space-x-2 focus:outline-none">
            <span class="text-gray-800">{{ auth()->user()->nama }}</span>
            <i class="fas fa-user-circle text-2xl text-indigo-800"></i>
        </button>

        <!-- Dropdown Menu -->
        <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50">
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                <i class="fas fa-user-edit mr-2"></i> Edit Profil
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
            </form>
        </div>
    </div>
</header>

<script>
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Tutup dropdown jika klik di luar
    window.addEventListener('click', function (e) {
        const dropdown = document.getElementById('profileDropdown');
        const button = e.target.closest('[onclick="toggleProfileDropdown()"]');
        if (!button && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
