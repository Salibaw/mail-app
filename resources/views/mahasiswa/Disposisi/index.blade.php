@extends('mahasiswa.layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Disposisi Surat Masuk untuk Anda</h2>

    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mt-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nomor Agenda Surat</th>
                    <th>Perihal Surat</th>
                    <th>Dari (Pemberi Disposisi)</th>
                    <th>Instruksi</th>
                    <th>Status</th>
                    <th>Tanggal Disposisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($disposisiDiterima as $disposisi)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $disposisi->suratMasuk->nomor_agenda ?? 'N/A' }}</td>
                        <td>{{ $disposisi->suratMasuk->perihal ?? 'N/A' }}</td>
                        <td>{{ $disposisi->dariUser->nama ?? 'N/A' }}</td>
                        <td>{{ Str::limit($disposisi->instruksi, 50) }}</td>
                        <td><span class="badge bg-primary">{{ $disposisi->status_disposisi }}</span></td>
                        <td>{{ $disposisi->tanggal_disposisi->translatedFormat('d M Y H:i') }}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showDisposisiModal" data-id="{{ $disposisi->id }}">Lihat Detail</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada disposisi surat masuk untuk Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $disposisiDiterima->links() }}
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Show Disposisi -->
<div class="modal fade" id="showDisposisiModal" tabindex="-1" aria-labelledby="showDisposisiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showDisposisiModalLabel">Detail Disposisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Perihal Surat:</strong> <span id="disposisi_show_perihal_surat"></span></p>
                <p><strong>Nomor Agenda Surat:</strong> <span id="disposisi_show_nomor_agenda"></span></p>
                <p><strong>Dari (Pemberi Disposisi):</strong> <span id="disposisi_show_dari_user"></span></p>
                <p><strong>Kepada:</strong> <span id="disposisi_show_ke_user"></span></p>
                <p><strong>Tanggal Disposisi:</strong> <span id="disposisi_show_tanggal"></span></p>
                <p><strong>Status Disposisi:</strong> <span id="disposisi_show_status"></span></p>

                <hr>
                <h5>Instruksi:</h5>
                <div id="disposisi_show_instruksi" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;"></div>

                <h5 class="mt-3">Lampiran Surat Masuk:</h5>
                <div id="disposisi_show_lampiran_surat"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="disposisi_show_link_surat_masuk" class="btn btn-info">Lihat Detail Surat Masuk</a>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#showDisposisiModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var disposisiId = button.data('id');
            var modal = $(this);

            $.ajax({
                url: `/mahasiswa/disposisi/${disposisiId}`, // Adjust based on your show route for disposisi
                method: 'GET',
                success: function(data) {
                    modal.find('.modal-title').text('Detail Disposisi');
                    modal.find('#disposisi_show_perihal_surat').text(data.surat_masuk.perihal || 'N/A');
                    modal.find('#disposisi_show_nomor_agenda').text(data.surat_masuk.nomor_agenda || 'N/A');
                    modal.find('#disposisi_show_dari_user').text(data.dari_user.nama || 'N/A');
                    modal.find('#disposisi_show_ke_user').text(data.ke_user.nama || 'N/A');
                    modal.find('#disposisi_show_tanggal').text(new Date(data.tanggal_disposisi).toLocaleString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'}));
                    modal.find('#disposisi_show_status').text(data.status_disposisi);
                    modal.find('#disposisi_show_instruksi').html(data.instruksi ? data.instruksi.replace(/\n/g, '<br>') : '-');

                    if (data.surat_masuk && data.surat_masuk.lampiran) {
                        modal.find('#disposisi_show_lampiran_surat').html(`<a href="/storage/${data.surat_masuk.lampiran}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat Lampiran</a>`);
                    } else {
                        modal.find('#disposisi_show_lampiran_surat').html('-');
                    }

                    // Link to view full incoming letter details (if user has access)
                    if (data.surat_masuk && data.surat_masuk.id) {
                        // Mahasiswa biasanya tidak bisa melihat detail penuh Surat Masuk,
                        // Hanya disposisi. Jadi link ini mungkin tidak relevan atau perlu penyesuaian otorisasi.
                        // Untuk contoh ini, saya akan menyembunyikannya atau memastikan tidak ada rute yang cocok untuk mahasiswa
                        // atau router pimpinan/staff_tu untuk lihat surat masuk
                        // Untuk saat ini, kita bisa arahkan ke view disposisi saja.
                        modal.find('#disposisi_show_link_surat_masuk').hide(); // Sembunyikan untuk mahasiswa
                    } else {
                        modal.find('#disposisi_show_link_surat_masuk').hide();
                    }
                },
                error: function(xhr) {
                    alert('Gagal memuat detail disposisi.');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endpush