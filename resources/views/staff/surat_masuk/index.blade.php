@extends('staff.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manajemen Surat Masuk</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSuratMasukModal">Catat Surat Masuk Baru</button>
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
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showSuratMasukModal" data-id="{{ $surat->id }}">Lihat</button>
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

<div class="modal fade" id="createSuratMasukModal" tabindex="-1" aria-labelledby="createSuratMasukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSuratMasukModalLabel">Catat Surat Masuk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createSuratMasukForm" action="{{ route('staff.surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomor_surat" class="form-label">Nomor Surat Asli</label>
                        <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_surat" class="form-label">Tanggal Surat Asli</label>
                        <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_terima" class="form-label">Tanggal Terima</label>
                        <input type="date" class="form-control" id="tanggal_terima" name="tanggal_terima" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="pengirim_id" class="form-label">Pengirim</label>
                        <select class="form-select" id="pengirim_id" name="pengirim_id" required>
                            <option value="">Pilih Pengirim</option>
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->nama }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="perihal" class="form-label">Perihal</label>
                        <input type="text" class="form-control" id="perihal" name="perihal" required>
                    </div>
                    <div class="mb-3">
                        <label for="isi_ringkas" class="form-label">Isi Ringkas</label>
                        <textarea class="form-control" id="isi_ringkas" name="isi_ringkas" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="lampiran" class="form-label">Lampiran (File Dokumen)</label>
                        <input class="form-control" type="file" id="lampiran" name="lampiran">
                        <small class="form-text text-muted">Max 2MB, format: PDF, DOC, DOCX, JPG, JPEG, PNG.</small>
                    </div>
                    <div class="mb-3">
                        <label for="status_id" class="form-label">Status Awal Surat</label>
                        <select class="form-select" id="status_id" name="status_id" required>
                            <option value="">Pilih Status</option>
                            @foreach ($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->nama_status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="sifat_surat_id" class="form-label">Sifat Surat</label>
                        <select class="form-select" id="sifat_surat_id" name="sifat_surat_id" required>
                            <option value="">Pilih Sifat</option>
                            @foreach ($sifatSurat as $sifat)
                            <option value="{{ $sifat->id }}">{{ $sifat->nama_sifat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Surat Masuk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="showSuratMasukModal" tabindex="-1" aria-labelledby="showSuratMasukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSuratMasukModalLabel">Detail Surat Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nomor Agenda:</strong> <span id="show_sm_nomor_agenda"></span></p>
                <p><strong>Nomor Surat Asli:</strong> <span id="show_sm_nomor_surat"></span></p>
                <p><strong>Tanggal Surat Asli:</strong> <span id="show_sm_tanggal_surat"></span></p>
                <p><strong>Tanggal Terima:</strong> <span id="show_sm_tanggal_terima"></span></p>
                <p><strong>Pengirim:</strong> <span id="show_sm_pengirim"></span></p>
                <p><strong>Perihal:</strong> <span id="show_sm_perihal"></span></p>
                <p><strong>Status:</strong> <span id="show_sm_status"></span></p>
                <p><strong>Sifat Surat:</strong> <span id="show_sm_sifat_surat"></span></p>
                <p><strong>Dicatat Oleh:</strong> <span id="show_sm_dicatat_oleh"></span></p>
                <p><strong>Dicatat Pada:</strong> <span id="show_sm_dicatat_pada"></span></p>

                <hr>
                <h5>Isi Ringkas:</h5>
                <div id="show_sm_isi_ringkas" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;"></div>

                <h5 class="mt-3">Lampiran:</h5>
                <div id="show_sm_lampiran"></div>

                <h5 class="mt-3">Riwayat Disposisi:</h5>
                <ul id="show_sm_riwayat_disposisi" class="list-group">
                    {{-- Riwayat akan diisi oleh JS --}}
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="" id="btn_disposisi_sm" class="btn btn-success">Disposisi Surat</a>
            </div>
        </div>
    </div>
</div>


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

        // Tombol LIHAT
        document.querySelectorAll('button[data-bs-target="#showSuratMasukModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/staff/surat-masuk/${id}`, {
                        headers
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('show_sm_nomor_agenda').textContent = data.nomor_agenda ?? '-';
                        document.getElementById('show_sm_nomor_surat').textContent = data.nomor_surat ?? '-';
                        document.getElementById('show_sm_tanggal_surat').textContent = data.tanggal_surat ?? '-';
                        document.getElementById('show_sm_tanggal_terima').textContent = data.tanggal_terima ?? '-';
                        document.getElementById('show_sm_pengirim').textContent = data.pengirim ? `${data.pengirim.nama} (${data.pengirim.email})` : '-';
                        document.getElementById('show_sm_perihal').textContent = data.perihal ?? '-';
                        document.getElementById('show_sm_status').textContent = data.status?.nama_status ?? '-';
                        document.getElementById('show_sm_sifat_surat').textContent = data.sifat?.nama_sifat ?? '-';
                        document.getElementById('show_sm_dicatat_oleh').textContent = data.user?.nama ?? '-';
                        document.getElementById('show_sm_dicatat_pada').textContent = data.created_at ?? '-';
                        document.getElementById('show_sm_isi_ringkas').textContent = data.isi_ringkas ?? '-';

                        const lampiranDiv = document.getElementById('show_sm_lampiran');
                        lampiranDiv.innerHTML = data.lampiran ?
                            `<a href="/storage/${data.lampiran}" target="_blank">Lihat Lampiran</a>` :
                            '<span class="text-muted">Tidak ada lampiran</span>';

                        const riwayatList = document.getElementById('show_sm_riwayat_disposisi');
                        riwayatList.innerHTML = '';
                        (data.disposisi ?? []).forEach(item => {
                            const li = document.createElement('li');
                            li.classList.add('list-group-item');
                            li.innerHTML = `<strong>${item.penerima?.nama ?? 'N/A'}</strong> - ${item.keterangan ?? '-'}`;
                            riwayatList.appendChild(li);
                        });

                        document.getElementById('btn_disposisi_sm').href = `/staff/disposisi/create/${data.id}`;
                    })
                    .catch(error => {
                        console.error('Error fetching show data:', error);
                        alert('Gagal memuat detail surat masuk.');
                    });
            });
        });

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