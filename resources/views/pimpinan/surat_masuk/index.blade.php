@extends('pimpinan.layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Daftar Surat Masuk</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Agenda</th>
                    <th>No. Surat Asli</th>
                    <th>Pengirim</th>
                    <th>Perihal</th>
                    <th>Status</th>
                    <th>Tgl Terima</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratMasuk as $surat)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $surat->nomor_agenda }}</td>
                        <td>{{ $surat->nomor_surat }}</td>
                        <td>{{ $surat->pengirim->email }}</td>
                        <td>{{ $surat->perihal }}</td>
                        <td><span class="badge bg-secondary">{{ $surat->status->nama_status }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($surat->tanggal_terima)->translatedFormat('d M Y') }}</td>
                        <td>
                            <a href="{{ route('pimpinan.surat-masuk.show', $surat->id) }}" class="btn btn-info btn-sm">Lihat Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data surat masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $suratMasuk->links() }}
    </div>
</div>
@endsection