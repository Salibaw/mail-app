<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\Disposisi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the Pimpinan dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Jumlah surat keluar yang menunggu persetujuan Pimpinan
        $suratMenungguPersetujuan = SuratKeluar::where('penerima_id', $user->id)
            ->whereHas('status', function ($query) {
                $query->where('nama_status', 'Menunggu Persetujuan');
            })
            ->count();

        // 5 Surat keluar terbaru yang menunggu persetujuan
        $latestSuratMenungguPersetujuan = SuratKeluar::where('penerima_id', $user->id)
            ->whereHas('status', function ($query) {
                $query->where('nama_status', 'Menunggu Persetujuan');
            })
            ->with(['user', 'status', 'sifat'])
            ->latest()
            ->take(5)
            ->get();

        // 5 Surat masuk terbaru yang didisposisi ke pimpinan
        $disposisiMasukUntukPimpinan = Disposisi::where('ke_user_id', $user->id)
            ->with(['suratMasuk', 'dariUser'])
            ->latest()
            ->take(5)
            ->get();

        // Statistik umum
        $totalSuratMasuk = SuratMasuk::where('pengirim_id', Auth::id())->count();
        $totalSuratKeluar = SuratKeluar::where('user_id', Auth::id())->count();

        return view('pimpinan.dashboard', compact(
            'suratMenungguPersetujuan',
            'latestSuratMenungguPersetujuan',
            'disposisiMasukUntukPimpinan',
            'totalSuratMasuk',
            'totalSuratKeluar'
        ));
    }
}
