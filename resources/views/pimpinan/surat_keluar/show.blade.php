@extends('pimpinan.layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Detail Surat Keluar: {{ $suratKeluar->perihal }}</h2>

    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
   @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Perihal: {{ $suratKeluar->perihal }}</h5>
            <p class="card-text"><strong>Penerima:</strong> {{ $suratKeluar->penerima->email }}</p>
            <p class="card-text"><strong>Diajukan Oleh:</strong> {{ $suratKeluar->user->nama ?? 'N/A' }} ({{ $suratKeluar->user->email ?? 'N/A' }})</p>
            <p class="card-text"><strong>Status:</strong> <span class="badge {{
                $suratKeluar->status->nama_status == 'Menunggu Persetujuan' ? 'bg-warning' :
                ($suratKeluar->status->nama_status == 'Disetujui' ? 'bg-success' :
                ($suratKeluar->status->nama_status == 'Ditolak' ? 'bg-danger' : 'bg-secondary'))
            }}">{{ $suratKeluar->status->nama_status }}</span></p>
            <p class="card-text"><strong>Sifat Surat:</strong> {{ $suratKeluar->sifat->nama_sifat ?? 'N/A' }}</p>
            <p class="card-text"><strong>No. Surat:</strong> {{ $suratKeluar->nomor_surat ?? '-' }}</p>
            <p class="card-text"><strong>Tanggal Surat:</strong> {{ $suratKeluar->tanggal_surat ? \Carbon\Carbon::parse($suratKeluar->tanggal_surat)->translatedFormat('d F Y') : '-' }}</p>
            <p class="card-text"><strong>Diajukan Pada:</strong> {{ $suratKeluar->created_at->translatedFormat('d M Y H:i') }}</p>
            <p class="card-text"><strong>Terakhir Diperbarui:</strong> {{ $suratKeluar->updated_at->translatedFormat('d M Y H:i') }}</p>

            <hr>
            <h5>Isi Surat:</h5>
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;">
                {!! nl2br(e($suratKeluar->isi_surat)) !!}
            </div>

            @if($suratKeluar->lampiran)
                <h5 class="mt-3">Lampiran:</h5>
                <p><a href="{{ asset('storage/' . $suratKeluar->lampiran) }}" target="_blank" class="btn btn-outline-primary">Lihat / Unduh Lampiran</a></p>
            @endif

            <h5 class="mt-3">Riwayat Persetujuan/Penolakan:</h5>
            @if($suratKeluar->persetujuan->isEmpty())
                <p>Belum ada riwayat persetujuan atau penolakan.</p>
            @else
                <ul class="list-group">
                    @foreach ($suratKeluar->persetujuan as $persetujuan)
                        <li class="list-group-item">
                            <strong>{{ $persetujuan->status_persetujuan }}</strong> oleh {{ $persetujuan->penyetuju->nama ?? 'N/A' }} ({{ $persetujuan->penyetuju->userType->nama_tipe ?? 'N/A' }}) pada {{ $persetujuan->tanggal_persetujuan->translatedFormat('d M Y H:i') }}
                            @if($persetujuan->catatan)
                                <br>Catatan: <em>"{{ $persetujuan->catatan }}"</em>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-4">
                <a href="{{ route('pimpinan.surat-keluar.index') }}" class="btn btn-secondary me-2">Kembali</a>
                @if($suratKeluar->status->nama_status === 'Menunggu Persetujuan')
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#approveModal">Setujui</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Tolak</button>
                @elseif($suratKeluar->status->nama_status === 'Disetujui' && $suratKeluar->nomor_surat && $suratKeluar->tanggal_surat)
                     <a href="{{ route('pimpinan.surat-keluar.download', $suratKeluar->id) }}" target="_blank" class="btn btn-primary">Unduh PDF</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Setujui Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pimpinan.surat-keluar.approve', $suratKeluar->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui surat ini?</p>
                    <div class="mb-3">
                        <label for="catatan_persetujuan" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan_persetujuan" name="catatan_persetujuan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pimpinan.surat-keluar.reject', $suratKeluar->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak surat ini?</p>
                    <div class="mb-3">
                        <label for="catatan_penolakan" class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" id="catatan_penolakan" name="catatan_penolakan" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection