@extends('mahasiswa.layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Dashboard Mahasiswa, {{ Auth::user()->nama }}</h2>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Surat Diajukan</h5>
                    <p class="card-text fs-3">{{ $totalSuratDiajukan }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Menunggu Persetujuan</h5>
                    <p class="card-text fs-3">{{ $suratMenungguPersetujuan }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Surat Disetujui</h5>
                    <p class="card-text fs-3">{{ $suratDisetujui }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4 mb-3">5 Surat Terakhir yang Diajukan</h3>
    @if ($suratDiajukan->isEmpty())
    <div class="alert alert-info">Anda belum mengajukan surat apapun.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Perihal</th>
                    <th>Penerima</th>
                    <th>Status</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suratDiajukan as $surat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $surat->perihal }}</td>
                    <td>{{ $surat->penerima->email }}</td>
                    <td><span class="badge {{
                                $surat->status->nama_status == 'Menunggu Persetujuan' ? 'bg-warning text-dark' :
                                ($surat->status->nama_status == 'Disetujui' ? 'bg-success' :
                                ($surat->status->nama_status == 'Ditolak' ? 'bg-danger' : 'bg-secondary'))
                            }}">{{ $surat->status->nama_status }}</span></td>
                    <td>{{ $surat->created_at->translatedFormat('d M Y') }}</td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showSuratModal" data-id="{{ $surat->id }}">Lihat</button>
                        @if(in_array($surat->status->nama_status, ['Draft', 'Ditolak']))
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSuratModal" data-id="{{ $surat->id }}">Edit</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('mahasiswa.surat-keluar.index') }}" class="btn btn-success">
            Ajukan Surat Baru
        </a>
        <a href="{{ route('mahasiswa.surat-keluar.index') }}" class="btn btn-primary">Lihat Semua Surat</a>
    </div>
</div>
<!-- Modal untuk Lihat Surat -->
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
                <p><strong>Template Surat:</strong> <span id="show_template_surat"></span></p>
                <p><strong>Nomor Surat:</strong> <span id="show_nomor_surat"></span></p>
                <p><strong>Tanggal Surat:</strong> <span id="show_tanggal_surat"></span></p>
                <p><strong>Dibuat Pada:</strong> <span id="show_created_at"></span></p>
                <p><strong>Diperbarui Pada:</strong> <span id="show_updated_at"></span></p>
                <p><strong>Isi Surat:</strong>
                <div id="show_isi_surat"></div>
                </p>
                <p><strong>Lampiran:</strong> <span id="show_lampiran"></span></p>
                <p><strong>Riwayat Persetujuan:</strong>
                <ul id="show_riwayat_persetujuan" class="list-group"></ul>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Edit Surat -->
<div class="modal fade" id="editSuratModal" tabindex="-1" aria-labelledby="editSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSuratModalLabel">Edit Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSuratForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="edit_surat_id" name="surat_id">
                    <div class="mb-3">
                        <label for="edit_perihal" class="form-label">Perihal</label>
                        <input type="text" class="form-control" id="edit_perihal" name="perihal" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_penerima" class="form-label">Penerima</label>
                        <input type="email" class="form-control" id="edit_penerima" name="penerima" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_sifat_surat_id" class="form-label">Sifat Surat</label>
                        <select class="form-control" id="edit_sifat_surat_id" name="sifat_surat_id">
                            <option value="">Pilih Sifat Surat</option>
                            <!-- Isi opsi dari controller atau AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_template_surat_id" class="form-label">Template Surat</label>
                        <select class="form-control" id="edit_template_surat_id" name="template_surat_id">
                            <option value="">Tanpa Template</option>
                            <!-- Isi opsi dari controller atau AJAX -->
                        </select>
                    </div>
                    <div id="edit_isi_surat_section" class="mb-3">
                        <label for="edit_isi_surat_manual" class="form-label">Isi Surat</label>
                        <textarea class="form-control" id="edit_isi_surat_manual" name="isi_surat" rows="5"></textarea>
                    </div>
                    <div id="edit_placeholder_section" class="mb-3" style="display: none;">
                        <label class="form-label">Placeholder Fields</label>
                        <div id="edit_placeholder_fields"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lampiran" class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="edit_lampiran" name="lampiran">
                        <div id="edit_current_lampiran"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    // Pastikan Anda menempatkan JavaScript ini di bagian `scripts` dari `surat_keluar/index.blade.php`
    // atau di file JS terpisah yang dimuat oleh layout.
    // Ini adalah placeholder, detail implementasi AJAX akan ada di surat_keluar/index.blade.php
    $('#showSuratModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var suratId = button.data('id');
        var modal = $(this);

        $.ajax({
            url: `/mahasiswa/surat-keluar/${suratId}`, // Sesuaikan dengan route show Anda
            method: 'GET',
            success: function(data) {
                modal.find('.modal-title').text('Detail Surat: ' + data.perihal);
                // Isi detail surat ke modal
                modal.find('#show_perihal').text(data.perihal);
                modal.find('#show_penerima').text(data.penerima);
                modal.find('#show_status').html('<span class="badge ' +
                    (data.status.nama_status === 'Menunggu Persetujuan' ? 'bg-warning text-dark' :
                        (data.status.nama_status === 'Disetujui' ? 'bg-success' :
                            (data.status.nama_status === 'Ditolak' ? 'bg-danger' : 'bg-secondary'))) +
                    '">' + data.status.nama_status + '</span>');
                modal.find('#show_sifat_surat').text(data.sifat.nama_sifat || 'N/A');
                modal.find('#show_template_surat').text(data.template_surat ? data.template_surat.nama_template : '-');
                modal.find('#show_nomor_surat').text(data.nomor_surat || '-');
                modal.find('#show_tanggal_surat').text(data.tanggal_surat ? new Date(data.tanggal_surat).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                }) : '-');
                modal.find('#show_created_at').text(new Date(data.created_at).toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                modal.find('#show_updated_at').text(new Date(data.updated_at).toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                modal.find('#show_isi_surat').html(data.isi_surat ? data.isi_surat.replace(/\n/g, '<br>') : '-');

                // Lampiran
                if (data.lampiran) {
                    modal.find('#show_lampiran').html(`<a href="/storage/${data.lampiran}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat Lampiran</a>`);
                } else {
                    modal.find('#show_lampiran').html('-');
                }

                // Riwayat Persetujuan
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

    // Script to handle showing edit surat modals (reused from surat_keluar.index)
    $('#editSuratModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var suratId = button.data('id');
        var modal = $(this);

        modal.find('form').attr('action', `/mahasiswa/surat-keluar/${suratId}`);
        modal.find('#edit_surat_id').val(suratId); // Set hidden input for ID

        $.ajax({
            url: `/mahasiswa/surat-keluar/${suratId}`, // Sesuaikan dengan route show Anda
            method: 'GET',
            success: function(data) {
                modal.find('.modal-title').text('Edit Surat: ' + data.perihal);
                modal.find('#edit_perihal').val(data.perihal);
                modal.find('#edit_penerima').val(data.penerima);
                modal.find('#edit_sifat_surat_id').val(data.sifat_surat_id);
                modal.find('#edit_template_surat_id').val(data.template_surat_id);

                // Handle isi_surat and placeholders based on template selection
                if (data.template_surat_id) {
                    modal.find('#edit_isi_surat_section').hide();
                    modal.find('#edit_isi_surat_manual').val('');
                    modal.find('#edit_placeholder_section').show();
                    modal.find('#edit_placeholder_fields').empty();

                    $.ajax({
                        url: '{{ route("mahasiswa.surat-keluar.get-template-details") }}', // Sesuaikan dengan route getTemplateDetails Anda
                        type: 'GET',
                        data: {
                            template_id: data.template_surat_id
                        },
                        success: function(templateData) {
                            if (templateData.placeholders && templateData.placeholders.length > 0) {
                                $.each(templateData.placeholders, function(index, placeholder) {
                                    var label = placeholder.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                    // Try to pre-fill placeholder values from data.isi_surat
                                    var regex = new RegExp(`\\{\\{${placeholder}\\}\\}`, 'g');
                                    var valueFromContent = data.isi_surat.match(regex) ? '' : '';
                                    var initialValue = '';

                                    modal.find('#edit_placeholder_fields').append(
                                        '<div class="mb-2">' +
                                        '<label for="edit_data_placeholder_' + placeholder + '" class="form-label">' + label + '</label>' +
                                        '<input type="text" class="form-control" id="edit_data_placeholder_' + placeholder + '" name="data_placeholder[' + placeholder + ']" value="' + initialValue + '">' +
                                        '</div>'
                                    );
                                });
                            } else {
                                modal.find('#edit_placeholder_fields').append('<p>Tidak ada placeholder ditemukan dalam template ini.</p>');
                            }
                        }
                    });
                } else {
                    modal.find('#edit_isi_surat_section').show();
                    modal.find('#edit_isi_surat_manual').val(data.isi_surat);
                    modal.find('#edit_placeholder_section').hide();
                    modal.find('#edit_placeholder_fields').empty();
                }

                // Handle existing lampiran
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

    // Handle template change in edit modal
    $('#edit_template_surat_id').change(function() {
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
                error: function(xhr, status, error) {
                    console.error("Error fetching template details: " + error);
                    modal.find('#edit_placeholder_fields').html('<p class="text-danger">Gagal memuat detail template.</p>');
                }
            });
        } else {
            modal.find('#edit_isi_surat_section').show();
            modal.find('#edit_placeholder_section').hide();
            modal.find('#edit_placeholder_fields').empty();
        }
    });

    // Handle form submission via AJAX for Create/Edit
    $(document).on('submit', '#createSuratForm, #editSuratForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var formData = new FormData(this); // For file uploads

        // Append _method for PUT/DELETE requests
        if (method.toUpperCase() === 'PUT') {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: 'POST', // Always POST for FormData, _method will handle PUT/DELETE
            data: formData,
            processData: false, // Don't process the data
            contentType: false, // Don't set content type
            success: function(response) {
                if (response.success) {
                    alert(response.success);
                    // Close modal and refresh page/table
                    $('.modal').modal('hide');
                    location.reload(); // Simple refresh for now, consider partial table reload for better UX
                } else {
                    alert('Terjadi kesalahan: ' + (response.error || 'Unknown error.'));
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';
                for (var key in errors) {
                    errorMessage += errors[key][0] + '\n';
                }
                alert('Validasi Gagal:\n' + errorMessage);
                console.error(xhr.responseText);
            }
        });
    });

    // Handle Delete via AJAX
    $('#deleteSuratModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var suratId = button.data('id');
        var form = $(this).find('form');
        form.attr('action', `/mahasiswa/surat-keluar/${suratId}`);
    });

    $(document).on('submit', '#deleteSuratForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', '{{ csrf_token() }}'); // Manually add CSRF token

        $.ajax({
            url: url,
            type: 'POST', // Always POST for FormData, _method will handle DELETE
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
</script>
@endpush