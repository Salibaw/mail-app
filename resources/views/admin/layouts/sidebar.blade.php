<aside id="sidebar" class="w-64 bg-indigo-800 text-white h-screen flex flex-col transition-all duration-300 lg:w-64 md:w-64 hidden lg:block md:block">
    <!-- Logo -->
    <div class="p-4 text-2xl font-bold border-b border-indigo-700">
        <a href="{{ route('admin.dashboard') }}">SISUKAT</a>
    </div>

    <!-- Menu -->
    <nav class="flex-1 mt-4">
        <ul>
            <!-- Dashboard -->
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
            </li>
            <!-- User -->
            <li>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-users mr-3"></i>
                    User Management
                </a>
            </li>
            <!-- Role (UserType) -->
            <li>
                <a href="{{ route('admin.roles.index') }}"
                   class="flex items-center px-4 py-3 {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-user-tag mr-3"></i>
                    Role Management
                </a>
            </li>
            <!-- Sifat Surat -->
            <li>
                <a href="{{ route('admin.sifat-surat.index') }}"
                   class="flex items-center px-4 py-3 {{ request()->routeIs('admin.sifat-surat.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-file-alt mr-3"></i>
                    Sifat Surat
                </a>
            </li>
            <!-- Status Surat -->
            <li>
                <a href="{{ route('admin.status-surat.index') }}"
                   class="flex items-center px-4 py-3 {{ request()->routeIs('admin.status-surat.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-check-circle mr-3"></i>
                    Status Surat
                </a>
            </li>
            <!-- Template Surat -->
            <li>
                <a href="{{ route('admin.templates.index') }}"
                   class="flex items-center px-4 py-3 {{ request()->routeIs('admin.template-surat.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-file-word mr-3"></i>
                    Template Surat
                </a>
            </li>
        </ul>
    </nav>
</aside>