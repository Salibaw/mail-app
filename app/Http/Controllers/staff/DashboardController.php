<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use App\Models\SuratMasuk; // Import model SuratMasuk
use App\Models\SuratKeluar; // Import model SuratKeluar
use App\Models\Disposisi; // Import model Disposisi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang sedang login

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk peran Staf Tata Usaha (TU).
     */
    public function index()
    {
        $users = Auth::user();

        // Statistik umum
        $totalSuratMasuk = SuratMasuk::count();
        $totalSuratKeluar = SuratKeluar::count(); // Semua surat keluar di sistem
        

        // Statistik spesifik untuk Staf TU
        $suratMasukBelumDidisposisi = SuratMasuk::whereDoesntHave('disposisi')->count();
        $suratKeluarMenungguPersetujuan = SuratKeluar::whereHas('status', function($query) {
            $query->where('nama_status', 'Menunggu Persetujuan');
        })->count();

        // 5 Surat Masuk terbaru (untuk di dashboard)
        $latestSuratMasuk = SuratMasuk::latest()->take(5)->get();

        // 5 Pengajuan Surat Keluar terbaru yang perlu diverifikasi/diproses oleh Staff TU
        $latestSuratKeluarPengajuan = SuratKeluar::whereHas('status', function($query) {
                                            $query->whereIn('nama_status', ['Draft', 'Menunggu Persetujuan', 'Ditolak']);
                                        })
                                        ->latest()
                                        ->take(5)
                                        ->get();

        // 5 Disposisi terbaru yang diterima oleh Staff TU (jika ada disposisi yang ditujukan ke mereka)
        $disposisiDiterima = Disposisi::where('ke_user_id', $users->id)
                                    ->with('suratMasuk')
                                    ->latest()
                                    ->take(5)
                                    ->get();

       return view('staff.dashboard', compact(
    'totalSuratMasuk',
    'totalSuratKeluar',
    'suratMasukBelumDidisposisi',
    'suratKeluarMenungguPersetujuan',
    'latestSuratMasuk',
    'latestSuratKeluarPengajuan',
    'disposisiDiterima','users'
));
    }
}