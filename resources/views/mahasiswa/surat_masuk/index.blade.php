@extends('mahasiswa.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Daftar Surat Masuk Anda</h2>
        <!-- No "Ajukan Surat Baru" button for incoming letters -->
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
                    <th>Nomor Surat</th>
                    <th>Perihal</th>
                    <th>Pengirim</th>
                    <th>Sifat Surat</th>
                    <th>Tanggal Terima</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratMasuk as $surat)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $surat->nomor_surat ?? 'N/A' }}</td>
                        <td>{{ $surat->perihal }}</td>
                        <td>{{ $surat->pengirim ? $surat->pengirim->nama . ' (' . $surat->pengirim->email . ')' : 'N/A' }}</td>
                        <td>{{ $surat->sifat->nama_sifat ?? 'N/A' }}</td>
                        <td>{{ $surat->tanggal_terima ? $surat->tanggal_terima->translatedFormat('d M Y') : 'N/A' }}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showSuratModal{{ $surat->id }}" data-id="{{ $surat->id }}">Lihat</button>
                            @if($surat->lampiran)
                                <a href="{{ asset('storage/' . $surat->lampiran) }}" target="_blank" class="btn btn-success btn-sm">Download</a>
                            @endif
                            @if($surat->disposisi->isNotEmpty())
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#showDisposisiModal" data-id="{{ $surat->id }}">Disposisi</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Anda belum menerima surat apapun.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $suratMasuk->links() }}
    </div>
</div>
<!-- Modal Show Surat -->
@foreach($suratMasuk as $surat)
<div class="modal fade" id="showSuratModal{{ $surat->id }}" tabindex="-1" aria-labelledby="showSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSuratModalLabel">Detail Surat Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p><strong>Nomor Agenda:</strong> <span id="show_nomor_agenda">{{$surat->nomor_agenda}}</span></p>
                <p><strong>Nomor Surat:</strong> <span id="show_nomor_surat"></span>{{$surat->nomor_surat}}</p>
                <p><strong>Perihal:</strong> <span id="show_perihal">{{$surat->perihal}}</span></p>
                <p><strong>Pengirim:</strong> <span id="show_pengirim"></span>{{$surat->pengirim->nama}}</p>
                <p><strong>Sifat Surat:</strong> <span id="show_sifat_surat"></span>{{$surat->sifat->nama_sifat}}</p>
                <p><strong>Tanggal Surat:</strong> <span id="show_tanggal_surat"></span>{{$surat->tanggal_surat}}</p>
                <p><strong>Tanggal Terima:</strong> <span id="show_tanggal_terima"></span>{{$surat->tanggal_terima}}</p>
                <p><strong>Status:</strong> <span id="show_status"></span>{{$surat->status->nama_status}}</p>
                <p><strong>Dibuat Pada:</strong> <span id="show_created_at"></span>{{$surat->created_at}}</p>
                <p><strong>Terakhir Diperbarui:</strong> <span id="show_updated_at"></span>{{$surat->updated_at}}</p>
                <hr>
                <h5>Isi Ringkas:</h5>
                <div id="show_isi_ringkas" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;">{{$surat->isi_ringkas}}</div>
                <h5 class="mt-3">Lampiran:</h5>
                @if($surat->lampiran)
                    <a href="{{ asset('storage/' . $surat->lampiran) }}" target="_blank" class="btn btn-secondary">Lihat Lampiran</a>
                @else
                    <p>Tidak ada lampiran untuk surat ini.</p>
                @endif
                <div id="show_lampiran"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
