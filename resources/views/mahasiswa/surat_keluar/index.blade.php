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
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showSuratModal" data-id="{{ $surat->id }}">Lihat</button>
                            @if(in_array($surat->status->nama_status, ['Draft', 'Ditolak']))
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSuratModal" data-id="{{ $surat->id }}">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSuratModal" data-id="{{ $surat->id }}">Hapus</button>
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
<div class="modal fade" id="editSuratModal" tabindex="-1" aria-labelledby="editSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSuratModalLabel">Edit Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSuratForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="surat_id" id="edit_surat_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_perihal" class="form-label">Perihal Surat</label>
                        <input type="text" class="form-control" id="edit_perihal" name="perihal" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_penerima_id" class="form-label">Penerima Surat</label>
                        <select class="form-select" id="edit_penerima_id" name="penerima_id" required>
                            <option value="">Pilih Penerima</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->nama }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_sifat_surat_id" class="form-label">Sifat Surat</label>
                        <select class="form-select" id="edit_sifat_surat_id" name="sifat_surat_id" required>
                            <option value="">Pilih Sifat Surat</option>
                            @foreach ($sifatSurat as $sifat)
                                <option value="{{ $sifat->id }}">{{ $sifat->nama_sifat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_template_surat_id" class="form-label">Gunakan Template Surat (Opsional)</label>
                        <select class="form-select" id="edit_template_surat_id" name="template_surat_id">
                            <option value="">Pilih Template</option>
                            @foreach ($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->nama_template }} ({{ $template->jenis_surat }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Jika memilih template, isi surat akan otomatis terisi dan Anda bisa mengisi placeholder.</small>
                    </div>
                    <div id="edit_isi_surat_section" class="mb-3">
                        <label for="edit_isi_surat_manual" class="form-label">Isi Surat Manual (jika tidak pakai template)</label>
                        <textarea class="form-control" id="edit_isi_surat_manual" name="isi_surat_manual" rows="10"></textarea>
                    </div>
                    <div id="edit_placeholder_section" class="mb-3" style="display:none;">
                        <h5>Isi Placeholder Template:</h5>
                        <div id="edit_placeholder_fields"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lampiran" class="form-label">Lampiran (Opsional)</label>
                        <div id="edit_current_lampiran" class="mb-2"></div>
                        <input class="form-control" type="file" id="edit_lampiran" name="lampiran">
                        <small class="form-text text-muted">Max 2MB, format: PDF, DOC, DOCX, JPG, JPEG, PNG.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Surat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Show Surat -->
<div class="modal fade" id="showSuratModal" tabindex="-1" aria-labelledby="showSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSuratModalLabel">Detail Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Perihal:</strong> <span id="show_perihal"></span></p>
                <p><strong>Penerima:</strong> <span id="show_penerima"></span></p>
                <p><strong>Status:</strong> <span id="show_status"></span></p>
                <p><strong>Sifat Surat:</strong> <span id="show_sifat_surat"></span></p>
                <p><strong>Menggunakan Template:</strong> <span id="show_template_surat"></span></p>
                <p><strong>Nomor Surat:</strong> <span id="show_nomor_surat"></span></p>
                <p><strong>Tanggal Surat:</strong> <span id="show_tanggal_surat"></span></p>
                <p><strong>Diajukan Pada:</strong> <span id="show_created_at"></span></p>
                <p><strong>Terakhir Diperbarui:</strong> <span id="show_updated_at"></span></p>
                <hr>
                <h5>Isi Surat:</h5>
                <div id="show_isi_surat" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;"></div>
                <h5 class="mt-3">Lampiran:</h5>
                <div id="show_lampiran"></div>
                <h5 class="mt-3">Riwayat Persetujuan/Penolakan:</h5>
                <ul id="show_riwayat_persetujuan" class="list-group"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

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
                data: { template_id: templateId },
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

    $('#showSuratModal').on('show.bs.modal', function (event) {
        console.log('showSuratModal opened');
        var button = $(event.relatedTarget);
        var suratId = button.data('id');
        var modal = $(this);

        $.ajax({
            url: `/mahasiswa/surat-keluar/${suratId}`,
            method: 'GET',
            success: function(data) {
                modal.find('.modal-title').text('Detail Surat: ' + data.perihal);
                modal.find('#show_perihal').text(data.perihal);
                modal.find('#show_penerima').text(data.penerima ? data.penerima.nama + ' (' + data.penerima.email + ')' : 'N/A');
                modal.find('#show_status').html('<span class="badge ' +
                    (data.status.nama_status === 'Menunggu Persetujuan' ? 'bg-warning text-dark' :
                    (data.status.nama_status === 'Disetujui' ? 'bg-success' :
                    (data.status.nama_status === 'Ditolak' ? 'bg-danger' : 'bg-secondary'))) +
                    '">' + data.status.nama_status + '</span>');
                modal.find('#show_sifat_surat').text(data.sifat ? data.sifat.nama_sifat : 'N/A');
                modal.find('#show_template_surat').text(data.template_surat ? data.template_surat.nama_template : '-');
                modal.find('#show_nomor_surat').text(data.nomor_surat || '-');
                modal.find('#show_tanggal_surat').text(data.tanggal_surat ? new Date(data.tanggal_surat).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'}) : '-');
                modal.find('#show_created_at').text(new Date(data.created_at).toLocaleString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'}));
                modal.find('#show_updated_at').text(new Date(data.updated_at).toLocaleString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'}));
                modal.find('#show_isi_surat').html(data.isi_surat ? data.isi_surat.replace(/\n/g, '<br>') : '-');

                if (data.lampiran) {
                    modal.find('#show_lampiran').html(`<a href="/storage/${data.lampiran}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat Lampiran</a>`);
                } else {
                    modal.find('#show_lampiran').html('-');
                }

                var persetujuanHtml = '';
                if (data.persetujuan && data.persetujuan.length > 0) {
                    $.each(data.persetujuan, function(index, persetujuan) {
                        persetujuanHtml += `<li class="list-group-item">
                            <strong>${persetujuan.status_persetujuan}</strong> oleh ${persetujuan.penyetuju ? persetujuan.penyetuju.nama : 'N/A'} pada ${new Date(persetujuan.tanggal_persetujuan).toLocaleString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'})}
                            ${persetujuan.catatan ? '<br>Catatan: <em>"' + persetujuan.catatan + '"</em>' : ''}
                        </li>`;
                    });
                } else {
                    persetujuanHtml = '<p>Belum ada riwayat persetujuan atau penolakan.</p>';
                }
                modal.find('#show_riwayat_persetujuan').html(persetujuanHtml);
            },
            error: function(xhr) {
                alert('Gagal memuat detail surat.');
                console.error(xhr.responseText);
            }
        });
    });

    $('#editSuratModal').on('show.bs.modal', function (event) {
        console.log('editSuratModal opened');
        var button = $(event.relatedTarget);
        var suratId = button.data('id');
        var modal = $(this);

        $('#editSuratForm')[0].reset();
        modal.find('#edit_surat_id').val(suratId);
        modal.find('#edit_current_lampiran').html('');
        modal.find('#edit_isi_surat_section').show();
        modal.find('#edit_placeholder_section').hide();
        modal.find('#edit_placeholder_fields').empty();

        $.ajax({
            url: `/mahasiswa/surat-keluar/${suratId}`,
            method: 'GET',
            success: function(data) {
                modal.find('.modal-title').text('Edit Surat: ' + data.perihal);
                modal.find('#edit_perihal').val(data.perihal);
                modal.find('#edit_penerima_id').val(data.penerima_id);
                modal.find('#edit_sifat_surat_id').val(data.sifat_surat_id);
                modal.find('#edit_template_surat_id').val(data.template_surat_id);

                if (data.template_surat_id) {
                    $('#edit_template_surat_id').trigger('change');
                    modal.find('#edit_isi_surat_manual').val('');
                } else {
                    modal.find('#edit_isi_surat_section').show();
                    modal.find('#edit_isi_surat_manual').val(data.isi_surat);
                    modal.find('#edit_placeholder_section').hide();
                    modal.find('#edit_placeholder_fields').empty();
                }

                if (data.lampiran) {
                    modal.find('#edit_current_lampiran').html(`<p>Lampiran saat ini: <a href="/storage/${data.lampiran}" target="_blank">Lihat Lampiran</a></p><small class="form-text text-muted">Unggah file baru untuk mengganti lampiran.</small>`);
                } else {
                    modal.find('#edit_current_lampiran').html('');
                }
            },
            error: function(xhr) {
                alert('Gagal memuat detail surat untuk edit.');
                console.error(xhr.responseText);
            }
        });
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
                data: { template_id: templateId },
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

    $('#deleteSuratModal').on('show.bs.modal', function (event) {
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