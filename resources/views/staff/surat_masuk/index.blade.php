@extends('staff.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manajemen Surat Masuk</h2>
        <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSuratMasukModal">Catat Surat Masuk Baru</button> -->
    </div>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Agenda</th>
                    <th>No. Surat</th>
                    <th>Pengirim</th>
                    <th>Perihal</th>
                    <th>Status</th>
                    <th>Tgl Terima</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratMasuk as $surat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $surat->nomor_agenda }}</td>
                    <td>{{ $surat->nomor_surat }}</td>
                    <td>{{ $surat->pengirim->email }}</td>
                    <td>{{ $surat->perihal }}</td>
                    <td><span class="badge bg-secondary">{{ $surat->status->nama_status }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($surat->tanggal_terima)->translatedFormat('d M Y') }}</td>
                    <td>
                        <a href="{{ route('staff.surat-masuk.show', $surat->id) }}" class="btn btn-info btn-sm">Lihat Detail</a>
                        <a href="{{ route('staff.disposisi.create', $surat->id) }}" class="btn btn-success btn-sm">Disposisi</a>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSuratMasukModal" data-id="{{ $surat->id }}">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data surat masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $suratMasuk->links() }}
    </div>
</div>

<!--  -->
@foreach($suratMasuk as $surat)
<div class="modal fade" id="deleteSuratMasukModal" tabindex="-1" aria-labelledby="deleteSuratMasukModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSuratMasukModalLabel">Konfirmasi Hapus Surat Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteSuratMasukForm" method="POST">
                @csrf
                {{-- _method will be added by JS --}}
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus surat masuk ini? Aksi ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CSRF Token Setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const headers = {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        };

        // Tombol EDIT
        document.querySelectorAll('button[data-bs-target="#editSuratMasukModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/staff/surat-masuk/${id}/edit`, {
                        headers
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        const form = document.getElementById('editSuratMasukForm');
                        form.action = `/staff/surat-masuk/${id}`;
                        document.getElementById('edit_sm_id').value = data.id;
                        document.getElementById('edit_sm_nomor_surat').value = data.nomor_surat ?? '';
                        document.getElementById('edit_sm_tanggal_surat').value = data.tanggal_surat ?? '';
                        document.getElementById('edit_sm_tanggal_terima').value = data.tanggal_terima ?? '';
                        document.getElementById('edit_sm_pengirim_id').value = data.pengirim_id ?? '';
                        document.getElementById('edit_sm_perihal').value = data.perihal ?? '';
                        document.getElementById('edit_sm_isi_ringkas').value = data.isi_ringkas ?? '';
                        document.getElementById('edit_sm_status_id').value = data.status_id ?? '';
                        document.getElementById('edit_sm_sifat_surat_id').value = data.sifat_surat_id ?? '';

                        const lampiranDiv = document.getElementById('edit_sm_current_lampiran');
                        lampiranDiv.innerHTML = data.lampiran ?
                            `<a href="/storage/${data.lampiran}" target="_blank">Lampiran Saat Ini</a>` :
                            '<span class="text-muted">Tidak ada lampiran</span>';
                    })
                    .catch(error => {
                        console.error('Error fetching edit data:', error);
                        alert('Gagal memuat data untuk edit surat masuk.');
                    });
            });
        });

        // Tombol HAPUS
        document.querySelectorAll('button[data-bs-target="#deleteSuratMasukModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const form = document.getElementById('deleteSuratMasukForm');
                form.action = `/staff/surat-masuk/${id}`;
            });
        });

        // Form Submission for Create and Edit
        ['createSuratMasukForm', 'editSuratMasukForm'].forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    submitButton.disabled = true;
                    submitButton.textContent = formId === 'createSuratMasukForm' ? 'Menyimpan...' : 'Mengupdate...';

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                            },
                        })
                        .then(response => {
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            submitButton.disabled = false;
                            submitButton.textContent = formId === 'createSuratMasukForm' ? 'Simpan Surat Masuk' : 'Update Surat Masuk';
                            if (data.success) {
                                alert(data.success);
                                window.location.reload();
                            } else {
                                alert('Terjadi kesalahan: ' + (data.error || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            submitButton.disabled = false;
                            submitButton.textContent = formId === 'createSuratMasukForm' ? 'Simpan Surat Masuk' : 'Update Surat Masuk';
                            console.error('Error submitting form:', error);
                            alert('Gagal menyimpan surat masuk.');
                        });
                });
            }
        });

        // Form Submission for Delete
        const deleteForm = document.getElementById('deleteSuratMasukForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.textContent = 'Menghapus...';

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Hapus';
                        if (data.success) {
                            alert(data.success);
                            window.location.reload();
                        } else {
                            alert('Terjadi kesalahan: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Hapus';
                        console.error('Error deleting:', error);
                        alert('Gagal menghapus surat masuk.');
                    });
            });
        }
    });
</script>
@endpush