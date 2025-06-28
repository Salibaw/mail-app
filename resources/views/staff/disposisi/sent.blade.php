@extends('staff.layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Disposisi Surat Masuk yang Anda Kirim</h2>

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
                    <th>Kepada</th>
                    <th>Instruksi</th>
                    <th>Status</th>
                    <th>Tanggal Disposisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($disposisiDibuat as $disposisi)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $disposisi->suratMasuk->nomor_agenda ?? 'N/A' }}</td>
                        <td>{{ $disposisi->suratMasuk->perihal ?? 'N/A' }}</td>
                        <td>{{ $disposisi->keUser->nama ?? 'N/A' }} ({{ $disposisi->keUser->role->name ?? 'N/A' }})</td>
                        <td>{{ Str::limit($disposisi->instruksi, 50) }}</td>
                        <td><span class="badge bg-primary">{{ $disposisi->status_disposisi }}</span></td>
                        <td>{{ $disposisi->tanggal_disposisi->translatedFormat('d M Y H:i') }}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showDisposisiModal" data-id="{{ $disposisi->id }}">Lihat Detail</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada disposisi surat masuk yang Anda kirim.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @foreach($disposisiDibuat as $disposisi)
    <div class="modal fade" id="showDisposisiModal" tabindex="-1" aria-labelledby="showDisposisiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showDisposisiModalLabel">Detail Disposisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Perihal Surat:</strong> <span id="disposisi_show_perihal_surat">{{$disposisi->suratMasuk->perihal}}</span></p>
                <p><strong>Nomor Agenda Surat:</strong> <span id="disposisi_show_nomor_agenda">{{ $disposisi->suratMasuk->nomor_agenda ?? 'N/A'}}</span></p>
                <p><strong>Dari (Pemberi Disposisi):</strong> <span id="disposisi_show_dari_user">{{ $disposisi->dariUser->nama}}</span></p>
                <p><strong>Kepada:</strong> <span id="disposisi_show_ke_user">{{ $disposisi->keUser->nama}}</span></p>
                <p><strong>Tanggal Disposisi:</strong> <span id="disposisi_show_tanggal">{{ $disposisi->tanggal_disposisi}}</span></p>
                <p><strong>Status Disposisi:</strong> <span id="disposisi_show_status">{{$disposisi->status_disposisi}}</span></p>

                <hr>
                <h5>Instruksi:</h5>
                <div id="disposisi_show_instruksi" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;">{{$disposisi->instruksi}}</div>

                <h5 class="mt-3">Lampiran Surat Masuk:</h5>
                @if($disposisi->suratMasuk->lampiran)
                    <a href="{{ asset('storage/' . $disposisi->suratMasuk->lampiran) }}" target="_blank" class="btn btn-secondary">Lihat Lampiran</a>
                @else
                    <p>Tidak ada lampiran untuk surat ini.</p>
                @endif

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{route('staff.surat-masuk.show', $disposisi->surat_masuk_id)}}" id="disposisi_show_link_surat_masuk" class="btn btn-info">Lihat Detail Surat Masuk</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
    <div class="d-flex justify-content-center">
        {{ $disposisiDibuat->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>
@endsection