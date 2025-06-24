<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\SuratMasuk; // Diperlukan untuk disposisi
use App\Models\User; // Diperlukan untuk daftar penerima disposisi
use App\Models\StatusSurat; // Diperlukan untuk memperbarui status surat masuk
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DisposisiController extends Controller
{
    /**
     * Menampilkan daftar disposisi yang diterima oleh Mahasiswa.
     */
    public function indexMahasiswa()
    {
        $user = Auth::user();
        if ($user->role->name !== 'mahasiswa') {
            abort(403, 'Akses ditolak.');
        }

        $disposisiDiterima = Disposisi::where('ke_user_id', $user->id)
                                     ->with(['suratMasuk', 'dariUser'])
                                     ->latest()
                                     ->paginate(10);

        return view('mahasiswa.disposisi.index', compact('disposisiDiterima'));
    }

    /**
     * Menampilkan daftar disposisi yang dibuat/dikirim oleh Staff TU.
     */
    public function indexSentByStaffTU()
    {
        $user = Auth::user();
        if ($user->role->name !== 'staff') {
            abort(403, 'Akses ditolak.');
        }

        $disposisiDibuat = Disposisi::where('dari_user_id', $user->id)
                                    ->with(['suratMasuk', 'keUser'])
                                    ->latest()
                                    ->paginate(10);

        return view('staff.disposisi.sent', compact('disposisiDibuat'));
    }

    /**
     * Menampilkan daftar disposisi yang diterima oleh Staff TU.
     */
    public function indexReceivedByStaffTU()
    {
        $user = Auth::user();
        if ($user->role->name !== 'staff') {
            abort(403, 'Akses ditolak.');
        }

        $disposisiDiterima = Disposisi::where('ke_user_id', $user->id)
                                     ->with(['suratMasuk', 'dariUser'])
                                     ->latest()
                                     ->paginate(10);

        return view('staff.disposisi.index_received', compact('disposisiDiterima'));
    }

    /**
     * Menampilkan daftar disposisi yang diterima oleh Pimpinan.
     */
    public function indexPimpinan()
    {
        $user = Auth::user();
        if ($user->role->name !== 'pimpinan') {
            abort(403, 'Akses ditolak.');
        }

        $disposisiDiterima = Disposisi::where('ke_user_id', $user->id)
                                     ->with(['suratMasuk', 'dariUser'])
                                     ->latest()
                                     ->paginate(10);

        return view('pimpinan.disposisi.index', compact('disposisiDiterima'));
    }

    // --- Implementasi untuk Dosen ---
    /**
     * Menampilkan daftar disposisi yang diterima oleh Dosen.
     */
    public function indexDosen()
    {
        $user = Auth::user();
        if ($user->role->name !== 'Dosen') {
            abort(403, 'Akses ditolak.');
        }

        $disposisiDiterima = Disposisi::where('ke_user_id', $user->id)
                                     ->with(['suratMasuk', 'dariUser'])
                                     ->latest()
                                     ->paginate(10);

        return view('dosen.disposisi.index', compact('disposisiDiterima'));
    }

    /**
     * Menampilkan form untuk membuat disposisi baru untuk surat masuk tertentu.
     * Hanya dapat diakses oleh Staff TU atau Pimpinan.
     */
    public function create(SuratMasuk $suratMasuk)
    {
        $user = Auth::user();
        if (!in_array($user->role->name, ['staff', 'pimpinan'])) {
            abort(403, 'Anda tidak diizinkan membuat disposisi.');
        }

        if ($suratMasuk->status->nama_status === 'Selesai') {
             return redirect()->back()->with('error', 'Surat ini sudah selesai dan tidak dapat didisposisi lagi.');
        }

        // Daftar user yang bisa menerima disposisi: Pimpinan, Dosen, Staf TU lain.
        $users = User::whereHas('role', function($query) {
                        $query->whereIn('name', ['pimpinan', 'dosen', 'staff']);
                    })
                    ->where('id', '!=', $user->id) // Hindari disposisi ke diri sendiri
                    ->get();

        // View untuk form disposisi (ini bukan modal, tapi halaman terpisah)
        return view('staff.disposisi.create', compact('suratMasuk', 'users'));
    }

    /**
     * Menyimpan disposisi baru ke storage.
     * Hanya dapat diakses oleh Staff TU atau Pimpinan.
     */
    public function store(Request $request, SuratMasuk $suratMasuk)
    {
        $user = Auth::user();
        if (!in_array($user->role->name, ['staff', 'pimpinan'])) {
            abort(403, 'Anda tidak diizinkan membuat disposisi.');
        }

        $request->validate([
            'ke_user_id' => 'required|exists:users,id',
            'instruksi' => 'nullable|string',
            'status_disposisi' => 'required|string|in:Diteruskan,Diterima,Selesai', // Sesuaikan opsi status disposisi
        ]);

        Disposisi::create([
            'surat_masuk_id' => $suratMasuk->id,
            'dari_user_id' => $user->id, // User yang membuat disposisi
            'ke_user_id' => $request->ke_user_id,
            'instruksi' => $request->instruksi,
            'tanggal_disposisi' => Carbon::now(),
            'status_disposisi' => $request->status_disposisi,
        ]);

        // Opsional: Perbarui status surat masuk menjadi 'Didisposisi'
        $statusDidisposisi = StatusSurat::where('nama_status', 'Didisposisi')->first();
        if ($statusDidisposisi && $suratMasuk->status_id !== $statusDidisposisi->id) {
             $suratMasuk->update(['status_id' => $statusDidisposisi->id]);
        }

        return redirect()->back()->with('success', 'Disposisi berhasil dibuat.');
    }

    /**
     * Menampilkan detail disposisi.
     * Dapat diakses oleh user yang menjadi pengirim (dari_user) atau penerima (ke_user) disposisi tersebut.
     * Mengembalikan JSON untuk modal.
     */
    public function show(Disposisi $disposisi)
    {
        $user = Auth::user();
        // Otorisasi: Hanya user yang terkait dengan disposisi (pengirim atau penerima) yang bisa melihat.
        // Admin dapat melihat semua.
        if ($disposisi->dari_user_id !== $user->id && $disposisi->ke_user_id !== $user->id && $user->role->name !== 'admin') {
            abort(403, 'Anda tidak diizinkan melihat disposisi ini.');
        }

        $disposisi->load(['suratMasuk.lampiran', 'dariUser.role', 'keUser.role']); // Load relasi yang diperlukan
        return response()->json($disposisi); // Mengembalikan JSON untuk modal
    }
}