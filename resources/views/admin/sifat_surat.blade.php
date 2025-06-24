@extends('admin.layouts.app')

@section('title', 'Manajemen Sifat Surat')

@section('page-title', 'Manajemen Sifat Surat')

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
        <h2 class="text-xl font-semibold text-gray-800">Daftar Sifat Surat</h2>
        <div class="space-x-2">
            <button onclick="openModal('createModal')" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Tambah Sifat Surat
            </button>
        </div>
    </div>

    <!-- Tabel Sifat Surat dengan DataTables -->
    <div class="overflow-x-auto">
        <table id="sifatSuratTable" class="min-w-full bg-white border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">No</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">nama_sifat</th>
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
        <h3 class="text-lg font-semibold mb-4">Tambah Sifat Surat</h3>
        <form action="{{ route('admin.sifat-surat.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="nama_sifat" class="block text-sm font-medium text-gray-700">Nama Sifat Surat</label>
                <input type="text" name="nama_sifat" id="nama_sifat" value="{{ old('nama_sifat') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="off" required>
                @error('nama_sifat')
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
        <h3 class="text-lg font-semibold mb-4">Edit Sifat Surat</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="edit_nama_sifat" class="block text-sm font-medium text-gray-700">nama_sifat Sifat Surat</label>
                <input type="text" name="nama_sifat" id="edit_nama_sifat"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="off" required>
                @error('nama_sifat')
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

    function openEditModal(id, nama_sifat) {
        document.getElementById('editForm').action = `/admin/sifat-surat/${id}`;
        document.getElementById('edit_nama_sifat').value = nama_sifat;
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
        var table = $('#sifatSuratTable').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route("admin.sifat-surat.index") }}',
                error: function(xhr, error, thrown) {
                    console.log('DataTables AJAX Error:', xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal memuat data: ' + (xhr.responseText || 'Unknown error'),
                        icon: 'error'
                    });
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_sifat', name: 'nama_sifat' },
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
                emptyTable: 'Tidak ada data sifat surat.',
                processing: 'Memproses...'
            }
        });

        // Pencarian langsung tanpa Enter
        $('#sifatSuratTable_filter input').unbind().bind('keyup', function(e) {
            table.search(this.value).draw();
        });

        // SweetAlert untuk konfirmasi hapus
        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data sifat surat akan dihapus permanen!',
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