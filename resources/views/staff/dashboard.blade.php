@extends('staff.layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Dashboard Staf Tata Usaha</h2>

    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Surat Masuk</h5>
                    <p class="card-text fs-3">{{ $totalSuratMasuk }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Surat Keluar</h5>
                    <p class="card-text fs-3">{{ $totalSuratKeluar }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">SM Belum Didisposisi</h5>
                    <p class="card-text fs-3">{{ $suratMasukBelumDidisposisi }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">SK Menunggu Persetujuan</h5>
                    <p class="card-text fs-3">{{ $suratKeluarMenungguPersetujuan }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4 mb-3">5 Surat Masuk Terbaru</h3>
    @if ($latestSuratMasuk->isEmpty())
        <div class="alert alert-info">Tidak ada surat masuk terbaru.</div>
    @else
        <div class="table-responsive table-responsive-sm-cards">
            <table class="table table-bordered table-hover bg-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Agenda</th>
                        <th>Pengirim</th>
                        <th>Perihal</th>
                        <th>Tgl Terima</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestSuratMasuk as $surat)
                        <tr>
                            <td data-label="#">{{ $loop->iteration }}</td>
                            <td data-label="No. Agenda">{{ $surat->nomor_agenda }}</td>
                            <td data-label="Pengirim">{{ $surat->pengirim->email }}</td>
                            <td data-label="Perihal">{{ $surat->perihal }}</td>
                            <td data-label="Tgl Terima">{{ \Carbon\Carbon::parse($surat->tanggal_terima)->translatedFormat('d M Y') }}</td>
                            <td data-label="Aksi">
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showSuratMasukModal" data-id="{{ $surat->id }}">Lihat</button>
                                <a href="{{ route('staff.disposisi.create', $surat->id) }}" class="btn btn-success btn-sm mt-1">Disposisi</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h3 class="mt-4 mb-3">5 Pengajuan Surat Keluar Terbaru (Perlu Verifikasi)</h3>
    @if ($latestSuratKeluarPengajuan->isEmpty())
        <div class="alert alert-info">Tidak ada pengajuan surat keluar terbaru yang menunggu verifikasi.</div>
    @else
        <div class="table-responsive table-responsive-sm-cards">
            <table class="table table-bordered table-hover bg-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Perihal</th>
                        <th>Diajukan Oleh</th>
                        <th>Status</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestSuratKeluarPengajuan as $surat)
                        <tr>
                            <td data-label="#">{{ $loop->iteration }}</td>
                            <td data-label="Perihal">{{ $surat->perihal }}</td>
                            <td data-label="Diajukan Oleh">{{ $surat->user->nama ?? 'N/A' }}</td>
                            <td data-label="Status"><span class="badge bg-warning text-dark">{{ $surat->status->nama_status }}</span></td>
                            <td data-label="Tanggal Pengajuan">{{ $surat->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td data-label="Aksi">
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showSuratKeluarModal" data-id="{{ $surat->id }}">Lihat</button>
                                <button type="button" class="btn btn-success btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#processSuratKeluarModal" data-id="{{ $surat->id }}">Proses</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h3 class="mt-4 mb-3">5 Disposisi Masuk Terbaru untuk Anda</h3>
    @if ($disposisiDiterima->isEmpty())
        <div class="alert alert-info">Tidak ada disposisi surat masuk untuk Anda.</div>
    @else
        <div class="table-responsive table-responsive-sm-cards">
            <table class="table table-bordered table-hover bg-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Agenda SM</th>
                        <th>Perihal SM</th>
                        <th>Dari</th>
                        <th>Instruksi</th>
                        <th>Tanggal Disposisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($disposisiDiterima as $disposisi)
                        <tr>
                            <td data-label="#">{{ $loop->iteration }}</td>
                            <td data-label="No. Agenda">{{ $disposisi->suratMasuk->nomor_agenda ?? 'N/A' }}</td>
                            <td data-label="Perihal">{{ $disposisi->suratMasuk->perihal ?? 'N/A' }}</td>
                            <td data-label="Dari">{{ $disposisi->dariUser->nama ?? 'N/A' }}</td>
                            <td data-label="Instruksi">{{ Str::limit($disposisi->instruksi, 50) }}</td>
                            <td data-label="Tanggal">{{ $disposisi->tanggal_disposisi->translatedFormat('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-4 d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createSuratMasukModal">Catat Surat Masuk Baru</button>
        <a href="{{ route('staff.surat-masuk.index') }}" class="btn btn-primary">Lihat Semua Surat Masuk</a>
        <a href="{{ route('staff.surat-keluar.index') }}" class="btn btn-info">Lihat Semua Pengajuan SK</a>
    </div>
</div>
@endsection