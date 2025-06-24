@extends('staff.layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Disposisi Surat Masuk yang Anda Terima</h2>

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
                    <th>No. Agenda Surat</th>
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
                            <a href="{{ route('staff.disposisi.create', $disposisi->suratMasuk->id) }}" class="btn btn-success btn-sm">Disposisi Lanjut</a>
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