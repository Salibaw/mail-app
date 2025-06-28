<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\SuratKeluar; // Import model SuratKeluar

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang sedang login

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk peran Mahasiswa.
     */
    public function index()
    {
        $user = Auth::user(); // Dapatkan user yang sedang login

        // Ambil data statistik dan surat terbaru yang diajukan oleh mahasiswa ini
        $totalSuratDiajukan = SuratKeluar::where('user_id', $user->id)->count();

        $suratMenungguPersetujuan = SuratKeluar::where('user_id', $user->id)
            ->whereHas('status', function ($query) {
                $query->where('nama_status', 'Menunggu Persetujuan');
            })
            ->count();

        $suratDisetujui = SuratKeluar::where('user_id', $user->id)
            ->whereHas('status', function ($query) {
                $query->where('nama_status', 'Disetujui');
            })
            ->count();

        $suratDiajukan = SuratKeluar::where('user_id', $user->id)
            ->with('status') // Muat relasi status
            ->latest()
            ->take(5) // Ambil 5 surat terbaru
            ->get();

        $suratKeluar = SuratKeluar::where($user->role->name, $user->id);
        return view('mahasiswa.dashboard', compact(
            'totalSuratDiajukan',
            'suratMenungguPersetujuan',
            'suratDisetujui',
            'suratDiajukan',
            'suratKeluar'
        ));
    }
}
