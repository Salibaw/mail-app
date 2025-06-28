@extends('staff.layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Disposisi Surat Masuk: {{ $suratMasuk->nomor_agenda }}</h2>
    <p>Perihal: {{ $suratMasuk->perihal }}</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.disposisi.store', $suratMasuk->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="ke_user_id" class="form-label">Tujukan kepada</label>
            <select class="form-select" id="ke_user_id" name="ke_user_id" required>
                <option value="">Pilih Penerima Disposisi</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('ke_user_id') == $user->id ? 'selected' : '' }}>{{ $user->nama }} ({{ $user->email ?? 'N/A' }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="instruksi" class="form-label">Instruksi/Catatan</label>
            <textarea class="form-control" id="instruksi" name="instruksi" rows="5">{{ old('instruksi') }}</textarea>
            @error('instruksi')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="status_disposisi" class="form-label">Status Disposisi</label>
            <select class="form-select" id="status_disposisi" name="status_disposisi" required>
                <option value="Diteruskan" {{ old('status_disposisi') == 'Diteruskan' ? 'selected' : '' }}>Diteruskan</option>
                <option value="Diterima" {{ old('status_disposisi') == 'Diterima' ? 'selected' : '' }}>Diterima (untuk disposisi balik)</option>
                <option value="Selesai" {{ old('status_disposisi') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
            @error('status_disposisi')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Simpan Disposisi</button>
        <a href="{{ route('staff.surat-masuk.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection