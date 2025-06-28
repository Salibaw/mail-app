@extends('mahasiswa.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Daftar Surat Keluar Anda</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSuratModal">Ajukan Surat Baru</button>
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
                    <th>Perihal</th>
                    <th>Penerima</th>
                    <th>Status</th>
                    <th>Sifat Surat</th>
                    <th>Tanggal Diajukan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratKeluar as $surat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $surat->perihal }}</td>
                    <td>{{ $surat->penerima->email }}</td>
                    <td><span class="badge {{
                            $surat->status->nama_status == 'Menunggu Persetujuan' ? 'bg-warning text-dark' :
                            ($surat->status->nama_status == 'Disetujui' ? 'bg-success' :
                            ($surat->status->nama_status == 'Ditolak' ? 'bg-danger' : 'bg-secondary'))
                        }}">{{ $surat->status->nama_status }}</span></td>
                    <td>{{ $surat->sifat->nama_sifat ?? 'N/A' }}</td>
                    <td>{{ $surat->created_at->translatedFormat('d M Y H:i') }}</td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showSuratModal{{ $surat->id }}" data-id="{{ $surat->id }}">Lihat</button>
                        @if(in_array($surat->status->nama_status, ['Draft', 'Ditolak']))
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSuratModal{{ $surat->id }}" data-id="{{ $surat->id }}">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSuratModal{{ $surat->id}}" data-id="{{ $surat->id }}">Hapus</button>
                        @endif
                        @if($surat->status->nama_status == 'Disetujui' && $surat->lampiran)
                        <a href="{{ asset('storage/' . $surat->lampiran) }}" target="_blank" class="btn btn-success btn-sm">Download</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Anda belum mengajukan surat apapun.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $suratKeluar->links() }}
    </div>
</div>

<!-- Modal Create Surat -->
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

<!-- Modal Edit Surat -->
@foreach ($suratKeluar as $surat)
<div class="modal fade" id="editSuratModal{{ $surat->id }}" tabindex="-1" aria-labelledby="editSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSuratModalLabel">Edit Surat: {{ $surat->perihal }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSuratForm" action="{{ route('mahasiswa.surat-keluar.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_surat_id" name="id" value="{{ $surat->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_perihal" class="form-label
">Perihal Surat</label>
                        <input type="text" class="form-control" id="edit_perihal" name="perihal" value="{{ $surat->perihal }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_penerima_id" class="form-label">Penerima Surat</label>
                        <select class="form-select" id="edit_penerima_id" name="penerima_id" required>
                            <option value="">Pilih Penerima</option>
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $surat->penerima_id == $user->id ? 'selected' : '' }}>{{ $user->nama }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_sifat_surat_id" class="form-label">Sifat Surat</label>
                        <select class="form-select" id="edit_sifat_surat_id" name="sifat_surat_id" required>
                            <option value="">Pilih Sifat Surat</option>
                            @foreach ($sifatSurat as $sifat)
                            <option value="{{ $sifat->id }}" {{ $surat->sifat_surat_id == $sifat->id ? 'selected' : '' }}>{{ $sifat->nama_sifat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_template_surat_id" class="form-label">Gunakan Template Surat (Opsional)</label>
                        <select class="form-select" id="edit_template_surat_id" name="template_surat_id">
                            <option value="">Pilih Template</option>
                            @foreach ($templates as $template)
                            <option value="{{ $template->id }}" {{ $surat->template_surat_id == $template->id ? 'selected' : '' }}>{{ $template->nama_template }} ({{ $template->jenis_surat }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Jika memilih template, isi surat akan otomatis terisi dan Anda bisa mengisi placeholder.</small>
                    </div>
                    <div id="edit_isi_surat_section" class="mb-3">
                        <label for="edit_isi_surat_manual" class="form-label">Isi Surat Manual (jika tidak pakai template)</label>
                        <textarea class="form-control" id="edit_isi_surat_manual" name="isi_surat_manual" rows="10">{{ $surat->isi_surat }}</textarea>
                    </div>
                    <div id="edit_placeholder_section" class="mb-3" style="display:none;">
                        <h5>Isi Placeholder Template:</h5>
                        <div id="edit_placeholder_fields"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lampiran" class="form-label
">Lampiran (Opsional)</label>

                        <input class="form-control" type="file" id="edit_lampiran" name="lampiran">
                        <small class="form-text text-muted">Max 2MB, format: PDF, DOC, DOCX, JPG, JPEG, PNG.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Show Surat -->
@foreach ($suratKeluar as $surat)
<div class="modal fade" id="showSuratModal{{ $surat->id}}" tabindex="-1" aria-labelledby="showSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSuratModalLabel">Detail Surat: {{ $surat->perihal }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Perihal:</strong> {{ $surat->perihal }}</p>
                <p><strong>Penerima:</strong> {{ $surat->penerima->email }}</p>
                <p><strong>Status:</strong> <span class="badge {{
                        $surat->status->nama_status == 'Menunggu Persetujuan' ? 'bg-warning text-dark' :
                        ($surat->status->nama_status == 'Disetujui' ? 'bg-success' :
                        ($surat->status->nama_status == 'Ditolak' ? 'bg-danger' : 'bg-secondary'))
                    }}">{{ $surat->status->nama_status }}</span></p>
                <p><strong>Sifat Surat:</strong> {{ $surat->sifat->nama_sifat ?? 'N/A' }}</p>
                <p><strong>Tanggal Diajukan:</strong> {{ $surat->created_at->translatedFormat('d M Y H:i') }}</p>
                <hr>
                <h5>Isi Surat:</h5>
                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;">{{ $surat->isi_surat }}</div>
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

<!-- Modal Delete Confirmation -->
<div class="modal fade" id="deleteSuratModal" tabindex="-1" aria-labelledby="deleteSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSuratModalLabel">Konfirmasi Hapus Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteSuratForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus surat ini? Aksi ini tidak dapat dibatalkan.</p>
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
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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
                    url: '{{ route("mahasiswa.surat-keluar.get-template-details") }}',
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


        $('#edit_template_surat_id').change(function() {
            console.log('Edit template changed, ID:', $(this).val());
            var templateId = $(this).val();
            var modal = $('#editSuratModal');
            if (templateId) {
                modal.find('#edit_isi_surat_section').hide();
                modal.find('#edit_isi_surat_manual').val('');
                modal.find('#edit_placeholder_section').show();
                modal.find('#edit_placeholder_fields').empty();

                $.ajax({
                    url: '{{ route("mahasiswa.surat-keluar.get-template-details") }}',
                    type: 'GET',
                    data: {
                        template_id: templateId
                    },
                    success: function(response) {
                        console.log('Edit template details response:', response);
                        if (response.placeholders && response.placeholders.length > 0) {
                            $.each(response.placeholders, function(index, placeholder) {
                                var label = placeholder.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                modal.find('#edit_placeholder_fields').append(
                                    '<div class="mb-2">' +
                                    '<label for="edit_data_placeholder_' + placeholder + '" class="form-label">' + label + '</label>' +
                                    '<input type="text" class="form-control" id="edit_data_placeholder_' + placeholder + '" name="data_placeholder[' + placeholder + ']">' +
                                    '</div>'
                                );
                            });
                        } else {
                            modal.find('#edit_placeholder_fields').append('<p>Tidak ada placeholder ditemukan dalam template ini.</p>');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching template details for edit: ", xhr.responseText);
                        modal.find('#edit_placeholder_fields').html('<p class="text-danger">Gagal memuat detail template.</p>');
                    }
                });
            } else {
                modal.find('#edit_isi_surat_section').show();
                modal.find('#edit_placeholder_section').hide();
                modal.find('#edit_placeholder_fields').empty();
            }
        });

        $(document).on('submit', '#createSuratForm, #editSuratForm', function(e) {
            e.preventDefault();
            console.log('Form submitted:', $(this).attr('id'));
            var form = $(this);
            var url = form.attr('action');
            var formData = new FormData(this);

            if (form.attr('id') === 'editSuratForm') {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Success response:', response);
                    if (response.success) {
                        alert(response.success);
                        $('.modal').modal('hide');
                        location.reload();
                    } else {
                        alert('Terjadi kesalahan: ' + (response.error || 'Unknown error.'));
                    }
                },
                error: function(xhr) {
                    console.error('Error response:', xhr.responseText);
                    var errors = xhr.responseJSON ? xhr.responseJSON.errors : {};
                    var errorMessage = '';
                    for (var key in errors) {
                        errorMessage += errors[key][0] + '\n';
                    }
                    alert('Validasi Gagal:\n' + (errorMessage || 'Terjadi kesalahan server.'));
                }
            });
        });

        $('#deleteSuratModal').on('show.bs.modal', function(event) {
            console.log('deleteSuratModal opened');
            var button = $(event.relatedTarget);
            var suratId = button.data('id');
            var form = $(this).find('form');
            form.attr('action', `/mahasiswa/surat-keluar/${suratId}`);
        });

        $(document).on('submit', '#deleteSuratForm', function(e) {
            e.preventDefault();
            console.log('Delete form submitted');
            var form = $(this);
            var url = form.attr('action');
            var formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.success);
                        $('#deleteSuratModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Terjadi kesalahan: ' + (response.error || 'Unknown error.'));
                    }
                },
                error: function(xhr) {
                    alert('Gagal menghapus surat.');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endpush