@extends('staff.layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Manajemen Surat Keluar (Pengajuan)</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSuratModal">Ajukan Surat Baru</button>
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
                    <th>Perihal</th>
                    <th>Penerima</th>
                    <th>Diajukan Oleh</th>
                    <th>Status</th>
                    <th>No. Surat</th>
                    <th>Tgl Pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratKeluar as $surat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $surat->perihal }}</td>
                    <td>{{ $surat->penerima->email }}</td>
                    <td>{{ $surat->user->nama ?? 'N/A' }} ({{ $surat->user->email ?? 'N/A' }})</td>
                    <td><span class="badge {{
                            $surat->status->nama_status == 'Menunggu Persetujuan' ? 'bg-warning text-dark' :
                            ($surat->status->nama_status == 'Disetujui' ? 'bg-success' :
                            ($surat->status->nama_status == 'Ditolak' ? 'bg-danger' : 'bg-secondary'))
                        }}">{{ $surat->status->nama_status }}</span></td>
                    <td>{{ $surat->nomor_surat ?? '-' }}</td>
                    <td>{{ $surat->created_at->translatedFormat('d M Y H:i') }}</td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showSuratKeluarModal" data-id="{{ $surat->id }}">Lihat</button>
                        @if(in_array($surat->status->nama_status, ['Draf', 'Menunggu Persetujuan', 'Ditolak']))
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#processSuratKeluarModal" data-id="{{ $surat->id }}">Proses No. Surat</button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectSuratKeluarModal" data-id="{{ $surat->id }}">Tolak</button>
                        @endif
                        @if($surat->status->nama_status == 'Disetujui' && $surat->nomor_surat && $surat->tanggal_surat)
                        <a href="{{ route('staff.surat-keluar.download', $surat->id) }}" target="_blank" class="btn btn-primary btn-sm">Unduh PDF</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada pengajuan surat keluar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $suratKeluar->links() }}
    </div>
</div>
<div class="modal fade" id="createSuratModal" tabindex="-1" aria-labelledby="createSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSuratModalLabel">Ajukan Surat Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createSuratForm" action="{{ route('mahasiswa.surat-keluar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="perihal" class="form-label">Perihal Surat</label>
                        <input type="text" class="form-control" id="perihal" name="perihal" required>
                    </div>
                    <div class="mb-3">
                        <label for="penerima_id" class="form-label">Penerima Surat</label>
                        <select class="form-select" id="penerima_id" name="penerima_id" required>
                            <option value="">Pilih Penerima</option>
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->nama }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="sifat_surat_id" class="form-label">Sifat Surat</label>
                        <select class="form-select" id="sifat_surat_id" name="sifat_surat_id" required>
                            <option value="">Pilih Sifat Surat</option>
                            @foreach ($sifatSurat as $sifat)
                            <option value="{{ $sifat->id }}">{{ $sifat->nama_sifat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="template_surat_id" class="form-label">Gunakan Template Surat (Opsional)</label>
                        <select class="form-select" id="template_surat_id" name="template_surat_id">
                            <option value="">Pilih Template</option>
                            @foreach ($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->nama_template }} ({{ $template->jenis_surat }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Jika memilih template, isi surat akan otomatis terisi dan Anda bisa mengisi placeholder.</small>
                    </div>
                    <div id="isi_surat_section" class="mb-3">
                        <label for="isi_surat_manual" class="form-label">Isi Surat Manual (jika tidak pakai template)</label>
                        <textarea class="form-control" id="isi_surat_manual" name="isi_surat_manual" rows="10"></textarea>
                    </div>
                    <div id="placeholder_section" class="mb-3" style="display:none;">
                        <h5>Isi Placeholder Template:</h5>
                        <div id="placeholder_fields"></div>
                    </div>
                    <div class="mb-3">
                        <label for="lampiran" class="form-label">Lampiran (Opsional)</label>
                        <input class="form-control" type="file" id="lampiran" name="lampiran">
                        <small class="form-text text-muted">Max 2MB, format: PDF, DOC, DOCX, JPG, JPEG, PNG.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Ajukan Surat</button>
                </div>
            </form>
        </div>
    </div>
</div>
@foreach($suratKeluar as $surat)
<div class="modal fade" id="showSuratKeluarModal" tabindex="-1" aria-labelledby="showSuratKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSuratKeluarModalLabel">Detail Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Perihal:</strong> {{$surat->perihal}}</p>
                <p><strong>Penerima:</strong> {{$surat->penerima->email}}</p>
                <p><strong>Diajukan Oleh:</strong>{{$surat->user->email}} </p>
                <p><strong>Status:</strong> {{$surat->status->nama_status}}</p>
                <p><strong>Sifat Surat:</strong>{{ $surat->sifat->nama_sifat ?? 'N/A' }} </p>
                <p><strong>Nomor Surat:</strong> {{$surat->nomor_surat}}</p>
                <p><strong>Tanggal Surat:</strong>{{ $surat->tanggal_surat ?? 'N/A' }} </p>
                <p><strong>Diajukan Pada:</strong>{{ $surat->created_at ?? 'N/A' }} </p>

                <hr>
                <h5>Isi Surat:</h5>
                <div id="show_sk_isi_surat" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;">{{$surat->isi_surat}}</div>

                @if($surat->lampiran)
                <h5 class="mt-3">Lampiran:</h5>
                <a href="{{ asset('storage/' . $surat->lampiran) }}" target="_blank" class="btn btn-secondary">Lihat Lampiran</a>
                @else
                <p>Tidak ada lampiran untuk surat ini.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Proses Surat Keluar --}}
<div class="modal fade" id="processSuratKeluarModal" tabindex="-1" aria-labelledby="processSuratKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processSuratKeluarModalLabel">Proses Penomoran Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="processSuratKeluarForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Memproses surat: <strong id="process_sk_perihal"></strong></p>
                    <div class="mb-3">
                        <label for="process_sk_nomor_surat" class="form-label">Nomor Surat Keluar</label>
                        <input type="text" class="form-control" id="process_sk_nomor_surat" name="nomor_surat" required>
                    </div>
                    <div class="mb-3">
                        <label for="process_sk_tanggal_surat" class="form-label">Tanggal Surat Keluar</label>
                        <input type="date" class="form-control" id="process_sk_tanggal_surat" name="tanggal_surat" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan & Teruskan ke Pimpinan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectSuratKeluarModal" tabindex="-1" aria-labelledby="rejectSuratKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectSuratKeluarModalLabel">Tolak Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectSuratKeluarForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Menolak surat: <strong id="reject_sk_perihal"></strong></p>
                    <div class="mb-3">
                        <label for="catatan_penolakan" class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" id="catatan_penolakan" name="catatan_penolakan" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Surat</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $('#createSuratModal').on('show.bs.modal', function() {
        console.log('createSuratModal opened');
        $('#createSuratForm')[0].reset();
        $('#isi_surat_section').show();
        $('#placeholder_section').hide();
        $('#placeholder_fields').empty();
        $('#template_surat_id').val('');
    });

    $('#template_surat_id').change(function() {
        console.log('Template changed, ID:', $(this).val());
        var templateId = $(this).val();
        if (templateId) {
            $('#isi_surat_section').hide();
            $('#isi_surat_manual').val('');
            $('#placeholder_section').show();
            $('#placeholder_fields').empty();

            $.ajax({
                url: '{{ route("staff.surat-keluar.get-template-details") }}',
                type: 'GET',
                data: {
                    template_id: templateId
                },
                success: function(response) {
                    console.log('Template details response:', response);
                    if (response.placeholders && response.placeholders.length > 0) {
                        $.each(response.placeholders, function(index, placeholder) {
                            var label = placeholder.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            $('#placeholder_fields').append(
                                '<div class="mb-2">' +
                                '<label for="data_placeholder_' + placeholder + '" class="form-label">' + label + '</label>' +
                                '<input type="text" class="form-control" id="data_placeholder_' + placeholder + '" name="data_placeholder[' + placeholder + ']">' +
                                '</div>'
                            );
                        });
                    } else {
                        $('#placeholder_fields').append('<p>Tidak ada placeholder ditemukan dalam template ini.</p>');
                    }
                },
                error: function(xhr) {
                    console.error("Error fetching template details: ", xhr.responseText);
                    $('#placeholder_fields').html('<p class="text-danger">Gagal memuat detail template.</p>');
                }
            });
        } else {
            $('#isi_surat_section').show();
            $('#placeholder_section').hide();
            $('#placeholder_fields').empty();
        }
    });
    const processModal = document.getElementById('processSuratKeluarModal');
    processModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const suratId = button.getAttribute('data-id');
        const perihal = button.closest('tr').querySelector('td:nth-child(2)').textContent;

        // Tampilkan perihal
        document.getElementById('process_sk_perihal').textContent = perihal;

        // Set form action sesuai ID
        const form = document.getElementById('processSuratKeluarForm');
        form.action = `/staff/surat-keluar/${suratId}/process`;
    });

    // Untuk modal penolakan surat
    const rejectModal = document.getElementById('rejectSuratKeluarModal');
    rejectModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const suratId = button.getAttribute('data-id');
        const perihal = button.closest('tr').querySelector('td:nth-child(2)').textContent;

        document.getElementById('reject_sk_perihal').textContent = perihal;
        document.getElementById('rejectSuratKeluarForm').action = `/staff/surat-keluar/${suratId}/reject-staff`;
    });
</script>
@endpush