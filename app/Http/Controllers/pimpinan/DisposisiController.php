<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiController extends Controller
{
    /**
     * Display a listing of dispositions received by this Pimpinan.
     */
    public function index()
    {
        $userId = Auth::id();
        $disposisiDiterima = Disposisi::where('ke_user_id', $userId)
                                     ->with(['suratMasuk', 'dariUser'])
                                     ->latest()
                                     ->paginate(10);
        return view('pimpinan.disposisi.index', compact('disposisiDiterima'));
    }

    /**
     * Display the specified disposition.
     */
    public function show(Disposisi $disposisi)
    {
        // Pastikan pimpinan hanya melihat disposisi yang ditujukan kepadanya
        if ($disposisi->ke_user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan melihat disposisi ini.');
        }
        $disposisi->load(['suratMasuk', 'dariUser', 'keUser']);
        return view('pimpinan.disposisi.show', compact('disposisi'));
    }

    // Pimpinan bisa jadi memiliki fitur untuk membuat disposisi ke staf di bawahnya
    // Jika ya, maka perlu metode create dan store juga, mirip dengan StaffTUDisposisiController
    // Untuk saat ini, diasumsikan Pimpinan hanya melihat dan menyetujui surat.
}