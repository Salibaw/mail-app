@extends('mahasiswa.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Daftar Surat Masuk Anda</h2>
        <!-- No "Ajukan Surat Baru" button for incoming letters -->
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
                    <th>Nomor Surat</th>
                    <th>Perihal</th>
                    <th>Pengirim</th>
                    <th>Sifat Surat</th>
                    <th>Tanggal Terima</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratMasuk as $surat)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $surat->nomor_surat ?? 'N/A' }}</td>
                        <td>{{ $surat->perihal }}</td>
                        <td>{{ $surat->pengirim ? $surat->pengirim->nama . ' (' . $surat->pengirim->email . ')' : 'N/A' }}</td>
                        <td>{{ $surat->sifat->nama_sifat ?? 'N/A' }}</td>
                        <td>{{ $surat->tanggal_terima ? $surat->tanggal_terima->translatedFormat('d M Y') : 'N/A' }}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showSuratModal" data-id="{{ $surat->id }}">Lihat</button>
                            @if($surat->lampiran)
                                <a href="{{ asset('storage/' . $surat->lampiran) }}" target="_blank" class="btn btn-success btn-sm">Download</a>
                            @endif
                            @if($surat->disposisi->isNotEmpty())
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#showDisposisiModal" data-id="{{ $surat->id }}">Disposisi</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Anda belum menerima surat apapun.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $suratMasuk->links() }}
    </div>
</div>
@endsection
<!-- Modal Show Surat -->
<div class="modal fade" id="showSuratModal" tabindex="-1" aria-labelledby="showSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSuratModalLabel">Detail Surat Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nomor Agenda:</strong> <span id="show_nomor_agenda"></span></p>
                <p><strong>Nomor Surat:</strong> <span id="show_nomor_surat"></span></p>
                <p><strong>Perihal:</strong> <span id="show_perihal"></span></p>
                <p><strong>Pengirim:</strong> <span id="show_pengirim"></span></p>
                <p><strong>Sifat Surat:</strong> <span id="show_sifat_surat"></span></p>
                <p><strong>Tanggal Surat:</strong> <span id="show_tanggal_surat"></span></p>
                <p><strong>Tanggal Terima:</strong> <span id="show_tanggal_terima"></span></p>
                <p><strong>Pencatat:</strong> <span id="show_pencatat"></span></p>
                <p><strong>Status:</strong> <span id="show_status"></span></p>
                <p><strong>Dibuat Pada:</strong> <span id="show_created_at"></span></p>
                <p><strong>Terakhir Diperbarui:</strong> <span id="show_updated_at"></span></p>
                <hr>
                <h5>Isi Ringkas:</h5>
                <div id="show_isi_ringkas" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;"></div>
                <h5 class="mt-3">Lampiran:</h5>
                <div id="show_lampiran"></div>
                <h5 class="mt-3">Riwayat Disposisi:</h5>
                <ul id="show_riwayat_disposisi" class="list-group"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Show Disposisi -->
<div class="modal fade" id="showDisposisiModal" tabindex="-1" aria-labelledby="showDisposisiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showDisposisiModalLabel">Detail Disposisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="disposisi_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#showSuratModal').on('show.bs.modal', function (event) {
        console.log('showSuratModal opened');
        var button = $(event.relatedTarget);
        var suratId = button.data('id');
        var modal = $(this);

        $.ajax({
            url: `/mahasiswa/surat-masuk/${suratId}`,
            method: 'GET',
            success: function(data) {
                modal.find('.modal-title').text('Detail Surat: ' + data.perihal);
                modal.find('#show_nomor_agenda').text(data.nomor_agenda || '-');
                modal.find('#show_nomor_surat').text(data.nomor_surat || '-');
                modal.find('#show_perihal').text(data.perihal);
                modal.find('#show_pengirim').text(data.pengirim ? data.pengirim.nama + ' (' + data.pengirim.email + ')' : 'N/A');
                modal.find('#show_sifat_surat').text(data.sifat ? data.sifat.nama_sifat : 'N/A');
                modal.find('#show_tanggal_surat').text(data.tanggal_surat ? new Date(data.tanggal_surat).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'}) : '-');
                modal.find('#show_tanggal_terima').text(data.tanggal_terima ? new Date(data.tanggal_terima).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'}) : '-');
                modal.find('#show_pencatat').text(data.user ? data.user.nama + ' (' + data.user.email + ')' : 'N/A');
                modal.find('#show_status').html('<span class="badge ' +
                    (data.status.nama_status === 'Menunggu Tindakan' ? 'bg-warning text-dark' :
                    (data.status.nama_status === 'Selesai' ? 'bg-success' :
                    (data.status.nama_status === 'Ditolak' ? 'bg-danger' : 'bg-secondary'))) +
                    '">' + data.status.nama_status + '</span>');
                modal.find('#show_created_at').text(new Date(data.created_at).toLocaleString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'}));
                modal.find('#show_updated_at').text(new Date(data.updated_at).toLocaleString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'}));
                modal.find('#show_isi_ringkas').html(data.isi_ringkas ? data.isi_ringkas.replace(/\n/g, '<br>') : '-');

                if (data.lampiran) {
                    modal.find('#show_lampiran').html(`<a href="/storage/${data.lampiran}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat Lampiran</a>`);
                } else {
                    modal.find('#show_lampiran').html('-');
                }

                var disposisiHtml = '';
                if (data.disposisi && data.disposisi.length > 0) {
                    $.each(data.disposisi, function(index, disposisi) {
                        disposisiHtml += `<li class="list-group-item">
                            <strong>${disposisi.tindakan}</strong> oleh ${disposisi.penerima ? disposisi.penerima.nama : 'N/A'} pada ${new Date(disposisi.tanggal_disposisi).toLocaleString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'})}
                            ${disposisi.catatan ? '<br>Catatan: <em>"' + disposisi.catatan + '"</em>' : ''}
                        </li>`;
                    });
                } else {
                    disposisiHtml = '<p>Belum ada riwayat disposisi.</p>';
                }
                modal.find('#show_riwayat_disposisi').html(disposisiHtml);
            },
            error: function(xhr) {
                alert('Gagal memuat detail surat.');
                console.error(xhr.responseText);
            }
        });
    });

    $('#showDisposisiModal').on('show.bs.modal', function (event) {
        console.log('showDisposisiModal opened');
        var button = $(event.relatedTarget);
        var suratId = button.data('id');
        var modal = $(this);

        $.ajax({
            url: `/mahasiswa/surat-masuk/${suratId}/disposisi`,
            method: 'GET',
            success: function(data) {
                modal.find('.modal-title').text('Disposisi untuk Surat: ' + data.surat.perihal);
                var disposisiHtml = '<ul class="list-group">';
                if (data.disposisi && data.disposisi.length > 0) {
                    $.each(data.disposisi, function(index, disposisi) {
                        disposisiHtml += `<li class="list-group-item">
                            <p><strong>Tindakan:</strong> ${disposisi.tindakan}</p>
                            <p><strong>Penerima:</strong> ${disposisi.penerima ? disposisi.penerima.nama + ' (' + disposisi.penerima.email + ')' : 'N/A'}</p>
                            <p><strong>Tanggal:</strong> ${new Date(disposisi.tanggal_disposisi).toLocaleString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'})}</p>
                            <p><strong>Catatan:</strong> ${disposisi.catatan || '-'}</p>
                        </li>`;
                    });
                } else {
                    disposisiHtml += '<li class="list-group-item">Tidak ada disposisi untuk surat ini.</li>';
                }
                disposisiHtml += '</ul>';
                modal.find('#disposisi_content').html(disposisiHtml);
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