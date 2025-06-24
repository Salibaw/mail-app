<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratMasukController extends Controller
{
    /**
     * Display a listing of all incoming letters (read-only for Pimpinan).
     */
    public function index()
    {
        $userId = Auth::id();
        $suratMasuk = SuratMasuk::with(['user', 'status', 'sifat', 'pengirim'])->where('user_id',$userId)->latest()->paginate(10);
        return view('pimpinan.surat_masuk.index', compact('suratMasuk'));
    }

    /**
     * Display the specified incoming letter.
     */
    public function show(SuratMasuk $suratMasuk)
    {
        $suratMasuk->load(['user', 'status', 'sifat', 'pengirim', 'disposisi.dariUser', 'disposisi.keUser']);
        return view('pimpinan.surat_masuk.show', compact('suratMasuk'));
    }
}
