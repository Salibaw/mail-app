@extends('admin.layouts.app')

@section('title', 'Manajemen Role')

@section('page-title', 'Manajemen Role')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <!-- Alert Notifikasi -->
    @if (session('success'))
        <div id="alert-success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded relative flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
            <button onclick="closeAlert('alert-success')" class="absolute right-4 text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div id="alert-error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded relative flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>{{ session('error') }}</span>
            <button onclick="closeAlert('alert-error')" class="absolute right-4 text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Role</h2>
        <button onclick="openModal('createModal')" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i> Tambah Role
        </button>
    </div>

    <!-- Tabel Role -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">No</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Nama Role</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $index => $role)
                    <tr class="border-b">
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">{{ $role->name }}</td>
                        <td class="px-6 py-4 flex space-x-2">
                            <button onclick="openEditModal({{ $role->id }}, '{{ addslashes($role->name) }}')"
                                    class="text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus role ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-600">Tidak ada data role.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Tambah Role</h3>
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Role</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                @error('name')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('createModal')" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Edit Role</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="edit_name" class="block text-sm font-medium text-gray-700">Nama Role</label>
                <input type="text" name="name" id="edit_name"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                @error('name')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('editModal')" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript untuk Modal dan Alert -->
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openEditModal(id, name) {
        document.getElementById('editForm').action = `/admin/roles/${id}`;
        document.getElementById('edit_name').value = name;
        openModal('editModal');
    }

    function closeAlert(alertId) {
        document.getElementById(alertId).classList.add('hidden');
    }

    // Auto-hide alert setelah 3 detik
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('[id^="alert-"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('hidden');
            }, 3000);
        });
    });
</script>
@endsection