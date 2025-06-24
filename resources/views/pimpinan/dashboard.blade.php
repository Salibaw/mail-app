@extends('pimpinan.layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Dashboard Pimpinan</h2>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Surat Menunggu Persetujuan</h5>
                    <p class="card-text fs-3">{{ $suratMenungguPersetujuan }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Surat Masuk</h5>
                    <p class="card-text fs-3">{{ $totalSuratMasuk }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Surat Keluar</h5>
                    <p class="card-text fs-3">{{ $totalSuratKeluar }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4 mb-3">5 Surat Keluar Terbaru Menunggu Persetujuan Anda</h3>
    @if ($latestSuratMenungguPersetujuan->isEmpty())
        <div class="alert alert-info">Tidak ada surat keluar yang menunggu persetujuan saat ini.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Perihal</th>
                        <th>Diajukan Oleh</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestSuratMenungguPersetujuan as $surat)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $surat->perihal }}</td>
                            <td>{{ $surat->user->nama ?? 'N/A' }} ({{ $surat->user->email ?? 'N/A' }})</td>
                            <td>{{ $surat->created_at->translatedFormat('d M Y') }}</td>
                            <td>
                                <a href="{{ route('pimpinan.surat-keluar.show', $surat->id) }}" class="btn btn-info btn-sm">Lihat & Proses</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h3 class="mt-4 mb-3">5 Disposisi Surat Masuk Terbaru untuk Anda</h3>
    @if ($disposisiMasukUntukPimpinan->isEmpty())
        <div class="alert alert-info">Tidak ada disposisi surat masuk untuk Anda.</div>
    @else
        <div class="table-responsive">
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
                    @foreach ($disposisiMasukUntukPimpinan as $disposisi)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $disposisi->surat_masuk->nomor_agenda ?? 'N/A' }}</td>
                            <td>{{ $disposisi->surat_masuk->perihal ?? 'N/A' }}</td>
                            <td>{{ $disposisi->dariUser->nama ?? 'N/A' }}</td>
                            <td>{{ $disposisi->instruksi }}</td>
                            <td>{{ $disposisi->tanggal_disposisi->translatedFormat('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('pimpinan.surat-keluar.index') }}" class="btn btn-primary me-2">Lihat Semua Pengajuan SK</a>
        <a href="{{ route('pimpinan.surat-masuk.index') }}" class="btn btn-secondary me-2">Lihat Semua Surat Masuk</a>
        <a href="{{ route('pimpinan.disposisi.index') }}" class="btn btn-info">Lihat Semua Disposisi Saya</a>
    </div>
</div>
@endsection