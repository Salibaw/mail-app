@extends('pimpinan.layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Daftar Pengajuan Surat Keluar</h2>

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
                            $surat->status->nama_status == 'Menunggu Persetujuan' ? 'bg-warning' :
                            ($surat->status->nama_status == 'Disetujui' ? 'bg-success' :
                            ($surat->status->nama_status == 'Ditolak' ? 'bg-danger' : 'bg-secondary'))
                        }}">{{ $surat->status->nama_status }}</span></td>
                        <td>{{ $surat->nomor_surat ?? '-' }}</td>
                        <td>{{ $surat->created_at->translatedFormat('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('pimpinan.surat-keluar.show', $surat->id) }}" class="btn btn-info btn-sm">Lihat & Proses</a>
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
@endsection