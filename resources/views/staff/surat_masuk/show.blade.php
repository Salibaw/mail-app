@extends('staff.layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Detail Surat Masuk: {{ $suratMasuk->nomor_agenda }}</h2>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Perihal: {{ $suratMasuk->perihal }}</h5>
            <p class="card-text"><strong>Nomor Agenda:</strong> {{ $suratMasuk->nomor_agenda }}</p>
            <p class="card-text"><strong>Nomor Surat Asli:</strong> {{ $suratMasuk->nomor_surat }}</p>
            <p class="card-text"><strong>Tanggal Surat Asli:</strong> {{ \Carbon\Carbon::parse($suratMasuk->tanggal_surat)->translatedFormat('d F Y') }}</p>
            <p class="card-text"><strong>Tanggal Terima:</strong> {{ \Carbon\Carbon::parse($suratMasuk->tanggal_terima)->translatedFormat('d F Y') }}</p>
            <p class="card-text"><strong>Pengirim:</strong> {{ $suratMasuk->pengirim->email }}</p>
            <p class="card-text"><strong>Status:</strong> <span class="badge bg-secondary">{{ $suratMasuk->status->nama_status }}</span></p>
            <p class="card-text"><strong>Sifat Surat:</strong> {{ $suratMasuk->sifat->nama_sifat ?? 'N/A' }}</p>
            <p class="card-text"><strong>Dicatat Oleh:</strong> {{ $suratMasuk->user->nama ?? 'N/A' }}</p>
            <p class="card-text"><strong>Dicatat Pada:</strong> {{ $suratMasuk->created_at->translatedFormat('d M Y H:i') }}</p>

            <hr>
            <h5>Isi Ringkas:</h5>
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;">
                {!! nl2br(e($suratMasuk->isi_ringkas)) !!}
            </div>

            @if($suratMasuk->lampiran)
                <h5 class="mt-3">Lampiran:</h5>
                <p><a href="{{ asset('storage/' . $suratMasuk->lampiran) }}" target="_blank" class="btn btn-outline-primary">Lihat / Unduh Lampiran</a></p>
            @endif

            <h5 class="mt-3">Riwayat Disposisi:</h5>
            @if($suratMasuk->disposisi->isEmpty())
                <p>Belum ada disposisi untuk surat ini.</p>
            @else
                <ul class="list-group">
                    @foreach ($suratMasuk->disposisi as $disposisi)
                        <li class="list-group-item">
                            <strong>Dari:</strong> {{ $disposisi->dariUser->nama ?? 'N/A' }} <br>
                            <strong>Kepada:</strong> {{ $disposisi->keUser->nama ?? 'N/A' }} <br>
                            <strong>Instruksi:</strong> <em>"{{ $disposisi->instruksi }}"</em> <br>
                            <strong>Status:</strong> {{ $disposisi->status_disposisi }} <br>
                            <strong>Tanggal:</strong> {{ $disposisi->tanggal_disposisi->translatedFormat('d M Y H:i') }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-4">
                <a href="{{ route('staff.surat-masuk.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection