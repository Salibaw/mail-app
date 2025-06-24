@extends('admin.layouts.app')

@section('title', 'Manajemen User')

@section('page-title', 'Manajemen User')

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
        <h2 class="text-xl font-semibold text-gray-800">Daftar User</h2>
        <div class="space-x-2">
            <button onclick="openModal('createModal')" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Tambah User
            </button>
            <button onclick="openModal('importModal')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-upload mr-2"></i> Impor User
            </button>
        </div>
    </div>

    <!-- Tabel User dengan DataTables -->
    <div class="overflow-x-auto">
        <table id="usersTable" class="min-w-full bg-white border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">No</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Nama</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">NIP/NIM</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Role</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal Create -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Tambah User</h3>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="name" required>
                @error('nama')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="email" required>
                @error('email')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="new-password" required>
                @error('password')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="nip_nim" class="block text-sm font-medium text-gray-700">NIP/NIM</label>
                <input type="text" name="nip_nim" id="nip_nim" value="{{ old('nip_nim') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="off">
                @error('nip_nim')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="telepon" class="block text-sm font-medium text-gray-700">Telepon</label>
                <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="tel">
                @error('telepon')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="alamat" id="alamat"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                          autocomplete="street-address">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role_id" id="role_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        autocomplete="off" required>
                    <option value="">Pilih Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')
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
        <h3 class="text-lg font-semibold mb-4">Edit User</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="edit_nama" class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="nama" id="edit_nama"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="name" required>
                @error('nama')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="edit_email"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="email" required>
                @error('email')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="edit_password" class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" id="edit_password"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="new-password">
                @error('password')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="edit_nip_nim" class="block text-sm font-medium text-gray-700">NIP/NIM</label>
                <input type="text" name="nip_nim" id="edit_nip_nim"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="off">
                @error('nip_nim')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="edit_telepon" class="block text-sm font-medium text-gray-700">Telepon</label>
                <input type="text" name="telepon" id="edit_telepon"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="tel">
                @error('telepon')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="edit_alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="alamat" id="edit_alamat"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                          autocomplete="street-address"></textarea>
                @error('alamat')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="edit_role_id" class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role_id" id="edit_role_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        autocomplete="off" required>
                    <option value="">Pilih Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')
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

<!-- ... Rest of the file (Import Modal, JavaScript, etc.) remains unchanged ... -->

<!-- JavaScript untuk Modal dan Alert -->
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openEditModal(id, nama, email, nip_nim, telepon, alamat, role_id) {
        document.getElementById('editForm').action = `/admin/users/${id}`;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_nip_nim').value = nip_nim || '';
        document.getElementById('edit_telepon').value = telepon || '';
        document.getElementById('edit_alamat').value = alamat || '';
        document.getElementById('edit_role_id').value = role_id || '';
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

@push('scripts')
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables CSS dan JS CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        var table = $('#usersTable').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("admin.users.index") }}',
                error: function(xhr, error, thrown) {
                    console.log('DataTables AJAX Error:', xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal memuat data: ' + (xhr.responseText || 'Unknown error'),
                        icon: 'error'
                    });
                }
            },
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf'
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'nip_nim', name: 'nip_nim' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role.name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ entri',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                },
                emptyTable: 'Tidak ada data user.',
                processing: 'Memproses...'
            }
        });

        // Pencarian langsung tanpa Enter
        $('#usersTable_filter input').unbind().bind('keyup', function(e) {
            table.search(this.value).draw();
        });

        // SweetAlert untuk konfirmasi hapus
        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data user akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush