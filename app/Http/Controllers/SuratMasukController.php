<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\StatusSurat;
use App\Models\SifatSurat;
use App\Models\User; // Untuk mendapatkan data pengguna
use App\Models\Disposisi; // Perlu diimpor untuk memeriksa disposisi terkait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Untuk str_pad atau slug
use Illuminate\Support\Facades\Storage; // Untuk menyimpan lampiran
use Illuminate\Support\Carbon; // Untuk tanggal
use Illuminate\Validation\Rule; // Untuk validasi unique

class SuratMasukController extends Controller
{
    /**
     * Menampilkan daftar Surat Masuk berdasarkan peran.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SuratMasuk::with(['user', 'status', 'sifat']);

        // Otorisasi dan Filter berdasarkan peran
        switch ($user->role->name) {
            case 'staff':
                $suratMasuk = SuratMasuk::with(['pengirim', 'status', 'sifat', 'user', 'disposisi'])
                    ->where('user_id', Auth::id())
                    ->paginate(10);
                $statuses = StatusSurat::all();
                $sifatSurat = SifatSurat::all();
                $users = User::select('id', 'nama', 'email')->get(); // For pengirim dropdown

                return view('staff.surat_masuk.index', compact('suratMasuk', 'statuses', 'sifatSurat', 'users'));
                break;
            case 'pimpinan':
                // Pimpinan bisa melihat semua surat masuk (read-only)
                // (Tidak perlu filter tambahan berdasarkan user_id)
                break;
            case 'mahasiswa':
                $user = Auth::user();
                if (!in_array($user->role->name, ['mahasiswa', 'dosen'])) {
                    abort(403, 'Akses ditolak.');
                }

                // Fetch incoming letters where the user is the recipient (assuming a relationship or field)
                $suratMasuk = SuratMasuk::with(['pengirim', 'sifat', 'user', 'disposisi.penerima'])
                    ->whereHas('disposisi', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->orWhere('user_id', $user->id)
                    ->paginate(10);

                $sifatSurat = SifatSurat::all();

                return view(
                    $user->role->name === 'mahasiswa' ? 'mahasiswa.surat_masuk.index' : 'dosen.surat_masuk.index',
                    compact('suratMasuk', 'sifatSurat')
                );
            case 'dosen':
                // Mahasiswa dan Dosen tidak melihat daftar surat masuk secara umum.
                // Mereka melihat disposisi yang ditujukan kepada mereka (ini ditangani di DisposisiController).
                // Jika ada kasus khusus Mahasiswa/Dosen bisa melihat 'public' surat masuk, tambahkan filter di sini.
                // Untuk saat ini, diasumsikan mereka tidak melihat index surat masuk ini.
                abort(403, 'Akses ditolak.');
                break;
            case 'admin':
                // Admin bisa melihat semua surat masuk
                break;
            default:
                abort(403, 'Akses ditolak.');
        }

        $suratMasuk = $query->latest()->paginate(10);

        // Siapkan data tambahan untuk view (misalnya, untuk modal create/edit di Staff TU)
        $statusSurat = StatusSurat::all();
        $sifatSurat = SifatSurat::all();

        // Pilih view berdasarkan peran
        if ($user->role->name === 'staff') {
            return view('staff.surat_masuk.index', compact('suratMasuk', 'statusSurat', 'sifatSurat'));
        } elseif ($user->role->name === 'pimpinan') {
            return view('pimpinan.surat_masuk.index', compact('suratMasuk'));
        }
        // Admin akan memiliki view dashboard tersendiri atau modul terpisah.
        // Jika admin perlu index seperti ini, bisa buat admin.surat_masuk.index
        return view('admin.surat_masuk.index', compact('suratMasuk')); // Contoh untuk Admin
    }

    /**
     * Menyimpan surat masuk baru (hanya untuk staff).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role->name !== 'staff') {
            abort(403, 'Anda tidak diizinkan mencatat surat masuk.');
        }

        $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'tanggal_terima' => 'required|date',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'isi_ringkas' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'status_id' => 'required|exists:status_surat,id',
            'sifat_surat_id' => 'required|exists:sifat_surat,id',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran_surat_masuk', 'public');
        }

        // Generate nomor agenda otomatis (contoh: SM/Tahun/Bulan/NomorUrut)
        $year = Carbon::now()->year;
        $month = Carbon::now()->format('m');
        $lastSuratMasuk = SuratMasuk::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $nextUrut = ($lastSuratMasuk) ? (int) substr($lastSuratMasuk->nomor_agenda, -4) + 1 : 1;
        $nomorAgenda = "SM/{$year}/{$month}/" . str_pad($nextUrut, 4, '0', STR_PAD_LEFT);

        SuratMasuk::create([
            'nomor_agenda' => $nomorAgenda,
            'nomor_surat' => $request->nomor_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_terima' => $request->tanggal_terima,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'isi_ringkas' => $request->isi_ringkas,
            'lampiran' => $lampiranPath,
            'user_id' => Auth::id(), // Pencatat surat adalah Staff TU yang login
            'status_id' => $request->status_id,
            'sifat_surat_id' => $request->sifat_surat_id,
        ]);

        return response()->json(['success' => 'Surat Masuk berhasil dicatat.']);
    }

    /**
     * Menampilkan detail surat masuk. Dapat diakses oleh staff, Pimpinan, dan Admin.
     */
    public function show(SuratMasuk $suratMasuk, $id)
    {
        $user = Auth::user();

        // Otorisasi: Hanya staff, Pimpinan, Admin yang bisa melihat detail penuh.
        // Mahasiswa/Dosen melihat melalui disposisi (jika disposisi surat ini ditujukan kepada mereka).
        if (!in_array($user->role->name, ['staff', 'pimpinan', 'admin'])) {
            // Jika Anda ingin Mahasiswa/Dosen bisa melihat detail SM jika disposisi ke mereka,
            // tambahkan logika pengecekan di sini, misalnya:
            // $hasDisposisiToUser = Disposisi::where('surat_masuk_id', $suratMasuk->id)
            //                               ->where('ke_user_id', $user->id)
            //                               ->exists();
            // if (!$hasDisposisiToUser) { abort(403, 'Akses ditolak.'); }
            abort(403, 'Anda tidak diizinkan melihat surat masuk ini.');
        } elseif ($user->role->name === 'mahasiswa' || $user->role->name === 'dosen') {
            // Jika Mahasiswa/Dosen, pastikan mereka memiliki disposisi terkait surat ini
            $surat = SuratMasuk::with(['pengirim', 'sifat', 'user', 'disposisi.penerima'])->findOrFail($id);
            if (
                !in_array(Auth::user()->role->name, ['mahasiswa', 'dosen']) ||
                ($surat->disposisi->penerima_id !== Auth::user()->id && $surat->pengirim_id !== Auth::user()->id)
            ) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return response()->json($surat);
        }

        $suratMasuk->load(['user', 'status', 'sifat', 'disposisi.dariUser', 'disposisi.keUser']);
        return response()->json($suratMasuk); // Mengembalikan JSON untuk modal
    }
    public function getDispositions($id)
    {
        $surat = SuratMasuk::with(['disposisi.penerima'])->findOrFail($id);
        if (
            !in_array(Auth::user()->role->name, ['mahasiswa', 'dosen']) ||
            ($surat->disposisi->penerima_id !== Auth::user()->id && $surat->pengirim_id !== Auth::user()->id)
        ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json(['surat' => $surat, 'disposisi' => $surat->disposisi]);
    }
    /**
     * Memperbarui surat masuk (hanya untuk staff).
     */
    public function update(Request $request, SuratMasuk $suratMasuk)
    {
        $user = Auth::user();
        if ($user->role->name !== 'staff') {
            abort(403, 'Anda tidak diizinkan memperbarui surat masuk.');
        }

        $request->validate([
            // Nomor agenda tidak boleh diubah
            'nomor_surat' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'tanggal_terima' => 'required|date',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'isi_ringkas' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'status_id' => 'required|exists:status_surat,id',
            'sifat_surat_id' => 'required|exists:sifat_surat,id',
        ]);

        $lampiranPath = $suratMasuk->lampiran;
        if ($request->hasFile('lampiran')) {
            if ($suratMasuk->lampiran && Storage::disk('public')->exists($suratMasuk->lampiran)) {
                Storage::disk('public')->delete($suratMasuk->lampiran);
            }
            $lampiranPath = $request->file('lampiran')->store('lampiran_surat_masuk', 'public');
        }

        $suratMasuk->update([
            'nomor_surat' => $request->nomor_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_terima' => $request->tanggal_terima,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'isi_ringkas' => $request->isi_ringkas,
            'lampiran' => $lampiranPath,
            'status_id' => $request->status_id,
            'sifat_surat_id' => $request->sifat_surat_id,
        ]);

        return response()->json(['success' => 'Surat Masuk berhasil diperbarui.']);
    }

    /**
     * Menghapus surat masuk (hanya untuk staff).
     */
    public function destroy(SuratMasuk $suratMasuk)
    {
        $user = Auth::user();
        if ($user->role->name !== 'staff') {
            abort(403, 'Anda tidak diizinkan menghapus surat masuk.');
        }

        // Pastikan tidak ada disposisi terkait sebelum menghapus surat masuk
        if ($suratMasuk->disposisi()->exists()) {
            return response()->json(['error' => 'Surat Masuk ini memiliki disposisi terkait. Harap hapus disposisi terlebih dahulu.'], 409);
        }

        if ($suratMasuk->lampiran && Storage::disk('public')->exists($suratMasuk->lampiran)) {
            Storage::disk('public')->delete($suratMasuk->lampiran);
        }
        $suratMasuk->delete();
        return response()->json(['success' => 'Surat Masuk berhasil dihapus.']);
    }
}
