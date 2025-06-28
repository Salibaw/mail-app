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
                            <td>
                                <span class="badge {{
                                    $surat->status->nama_status == 'Menunggu Persetujuan' ? 'bg-warning text-dark' :
                                    ($surat->status->nama_status == 'Disetujui' ? 'bg-success' :
                                    ($surat->status->nama_status == 'Ditolak' ? 'bg-danger' : 'bg-secondary'))
                                }}">{{ $surat->status->nama_status }}</span>
                            </td>
                            <td>{{ $surat->created_at->translatedFormat('d M Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#showSuratModal{{ $surat->id }}">Lihat</button>
                                @if (in_array($surat->status->nama_status, ['Draft', 'Ditolak']))
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editSuratModal" data-id="{{ $surat->id }}">Edit</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('mahasiswa.surat-keluar.index') }}" class="btn btn-success">Ajukan Surat Baru</a>
        <a href="{{ route('mahasiswa.surat-keluar.index') }}" class="btn btn-primary">Lihat Semua Surat</a>
    </div>
</div>

{{-- Modal Lihat Surat --}}
@foreach ($suratDiajukan as $surat)
<div class="modal fade" id="showSuratModal{{ $surat->id }}" tabindex="-1" aria-labelledby="showSuratModalLabel{{ $surat->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSuratModalLabel{{ $surat->id }}">Detail Surat: {{ $surat->perihal }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p><strong>Perihal:</strong> {{ $surat->perihal }}</p>
                <p><strong>Penerima:</strong> {{ $surat->penerima->email }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge {{
                        $surat->status->nama_status == 'Menunggu Persetujuan' ? 'bg-warning text-dark' :
                        ($surat->status->nama_status == 'Disetujui' ? 'bg-success' :
                        ($surat->status->nama_status == 'Ditolak' ? 'bg-danger' : 'bg-secondary'))
                    }}">{{ $surat->status->nama_status }}</span>
                </p>
                <p><strong>Sifat Surat:</strong> {{ $surat->sifat->nama_sifat ?? 'N/A' }}</p>
                <p><strong>Tanggal Diajukan:</strong> {{ $surat->created_at->translatedFormat('d M Y H:i') }}</p>
                <hr>
                <h5>Isi Surat:</h5>
                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;">
                    {{ $surat->isi_surat }}
                </div>
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

{{-- Modal Edit Surat --}}
<div class="modal fade" id="editSuratModal" tabindex="-1" aria-labelledby="editSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editSuratForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editSuratModalLabel">Edit Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
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
                            {{-- Opsi diisi secara dinamis --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_template_surat_id" class="form-label">Template Surat</label>
                        <select class="form-control" id="edit_template_surat_id" name="template_surat_id">
                            <option value="">Tanpa Template</option>
                            {{-- Opsi diisi secara dinamis --}}
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
