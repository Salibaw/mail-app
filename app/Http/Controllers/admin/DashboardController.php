<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Contoh data untuk dashboard admin
        $totalUsers = User::count();
        $totalSuratMasuk = SuratMasuk::count();
        $totalSuratKeluar = SuratKeluar::count();

        // Anda bisa menambahkan data statistik lain di sini
        $latestSuratMasuk = SuratMasuk::latest()->take(5)->get();
        $latestSuratKeluar = SuratKeluar::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalSuratMasuk',
            'totalSuratKeluar',
            'latestSuratMasuk',
            'latestSuratKeluar'
        ));
    }
}