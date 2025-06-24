<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\TemplateSurat;
use App\Models\StatusSurat;
use App\Models\SifatSurat;
use App\Models\SuratMasuk; // Diperlukan untuk Surat Masuk terkait
use App\Models\PersetujuanSuratKeluar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;


class SuratKeluarController extends Controller
{
    /**
     * Metode pembantu untuk mengambil data Surat Keluar berdasarkan peran.
     */
    protected function getSuratKeluarData(string $role, $userId = null)
    {
        $query = SuratKeluar::with(['user.role', 'status', 'sifat', 'templateSurat', 'persetujuan.penyetuju']);

        if ($role === 'mahasiswa' || $role === 'dosen') { // Dosen akan memiliki alur yang sama dengan Mahasiswa untuk pengajuan
            $query->where('user_id', $userId);
        } elseif ($role === 'staff') {
            // staff melihat semua surat keluar yang perlu diproses atau sudah diproses.
            // Bisa memfilter berdasarkan status yang relevan bagi Staff TU.
            $query->whereHas('status', function ($q) {
                $q->whereIn('nama_status', ['Draft', 'Menunggu Persetujuan', 'Ditolak', 'Disetujui']);
            });
        } elseif ($role === 'pimpinan') {
            // Pimpinan melihat surat yang menunggu persetujuan mereka, atau yang sudah mereka setujui/tolak.
            $query->whereHas('status', function ($q) {
                $q->whereIn('nama_status', ['Menunggu Persetujuan', 'Disetujui', 'Ditolak']);
            })->where(function (Builder $q) use ($userId) {
                $q->whereHas('status', function ($subQ) {
                    $subQ->where('nama_status', 'Menunggu Persetujuan'); // Surat yang masih pending untuk persetujuan Pimpinan
                })->orWhereHas('persetujuan', function ($subQ) use ($userId) {
                    $subQ->where('user_id_penyetuju', $userId); // Surat yang sudah diproses oleh Pimpinan ini
                });
            });
        }
        // Tambahkan kondisi untuk role 'admin' jika mereka juga melihat surat keluar.

        return $query->latest()->paginate(10);
    }

    /**
     * Menampilkan daftar Surat Keluar untuk peran Mahasiswa.
     */
    public function index() // Digunakan untuk Mahasiswa dan Dosen
    {
        $user = Auth::user();
        if (!in_array($user->role->name, ['mahasiswa', 'dosen'])) {
            abort(403, 'Akses ditolak.');
        }

        $suratKeluar = $this->getSuratKeluarData($user->role->name, $user->id);
        $templates = TemplateSurat::all(); // Diperlukan untuk form buat/edit surat
        $sifatSurat = SifatSurat::all();   // Diperlukan untuk form buat/edit surat
        $users = User::whereIn('role_id', $this->getAllowedRecipientRoleIds($user->role->name))
            ->select('id', 'nama', 'email')
            ->get();
        // Mahasiswa dan Dosen akan menggunakan view yang sama
        return view($user->role->name === 'mahasiswa' ? 'mahasiswa.surat_keluar.index' : 'dosen.surat_keluar.index', compact('suratKeluar', 'templates', 'sifatSurat', 'users'));
    }
    private function getAllowedRecipientRoleIds($roleName)
    {
        // Example: Adjust role IDs based on your role table
        if ($roleName === 'mahasiswa') {
            return [2, 3]; // Assume role_id 2 = dosen, 3 = staff
        } elseif ($roleName === 'dosen') {
            return [3, 4]; // Assume role_id 3 = staff, 4 = admin
        }
        return [];
    }
    /**
     * Menampilkan daftar Surat Keluar untuk peran staff.
     */
    public function indexStaffTU()
    {
        if (!Auth::user()->role || Auth::user()->role->name !== 'staff') {
            abort(403, 'Akses ditolak.');
        }
        $user = Auth::user();
        $templates = TemplateSurat::all(); // Diperlukan untuk form buat/edit surat
        $sifatSurat = SifatSurat::all();   // Diperlukan untuk form buat/edit surat
        $users = User::whereIn('role_id', $this->getAllowedRecipientRoleIds($user->role->name))
            ->select('id', 'nama', 'email')
            ->get();
        $suratKeluar = $this->getSuratKeluarData('staff');
        return view('staff.surat_keluar.index', compact('suratKeluar','templates', 'sifatSurat', 'users'));
    }

    /**
     * Menampilkan daftar Surat Keluar untuk peran Pimpinan.
     */
    public function indexPimpinan()
    {
        if (!Auth::user()->role || Auth::user()->role->name !== 'Pimpinan') {
            abort(403, 'Akses ditolak.');
        }

        $suratKeluar = $this->getSuratKeluarData('pimpinan', Auth::id());
        return view('pimpinan.surat_keluar.index', compact('suratKeluar'));
    }


    /**
     * Menyimpan surat keluar yang baru dibuat (oleh mahasiswa/Dosen).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role->name, ['mahasiswa', 'dosen', 'staff'])) {
            abort(403, 'Anda tidak diizinkan membuat surat.');
        }

        $request->validate([
            'perihal' => 'required|string|max:255',
            'penerima_id' => 'required|string|max:255',
            'sifat_surat_id' => 'required|exists:sifat_surats,id',
            'template_surat_id' => 'nullable|exists:template_surats,id',
            'isi_surat_manual' => 'nullable|string', // Isi manual jika tidak pakai template
            'data_placeholder' => 'nullable|array', // Data untuk placeholder
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $isiSuratFinal = $request->isi_surat_manual;

        if ($request->filled('template_surat_id')) {
            $template = TemplateSurat::findOrFail($request->template_surat_id);
            $isiSuratFinal = $template->isi_template;

            if ($request->has('data_placeholder') && is_array($request->data_placeholder)) {
                foreach ($request->data_placeholder as $key => $value) {
                    $isiSuratFinal = Str::replace("{{{$key}}}", $value, $isiSuratFinal);
                }
            }
            // Hapus placeholder yang tidak terisi atau yang tidak cocok dengan data_placeholder
            $isiSuratFinal = preg_replace('/\{\{[^}]+\}\}/', '', $isiSuratFinal);
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran_surat_keluar', 'public');
        }

        $statusMenungguPersetujuan = StatusSurat::where('nama_status', 'Menunggu Persetujuan')->firstOrFail();

        SuratKeluar::create([
            'perihal' => $request->perihal,
            'penerima_id' => $request->penerima_id,
            'isi_surat' => $isiSuratFinal,
            'lampiran' => $lampiranPath,
            'user_id' => $user->id, // ID pengguna yang mengajukan
            'status_id' => $statusMenungguPersetujuan->id, // Status awal selalu "Menunggu Persetujuan"
            'sifat_surat_id' => $request->sifat_surat_id,
            'template_surat_id' => $request->template_surat_id,
        ]);

        return response()->json(['success' => 'Pengajuan surat berhasil dikirim. Menunggu persetujuan.']);
    }

    /**
     * Menampilkan detail surat keluar. Dapat diakses oleh semua peran dengan otorisasi.
     */
    public function show(SuratKeluar $suratKeluar)
    {
        $user = Auth::user();

        // Otorisasi umum: Admin, Pimpinan, staff dapat melihat semua.
        // mahasiswa/Dosen hanya dapat melihat surat yang mereka ajukan.
        if (!in_array($user->role->name, ['Admin', 'Pimpinan', 'staff'])) {
            if (!in_array($user->role->name, ['mahasiswa', 'Dosen']) || $suratKeluar->user_id !== $user->id) {
                abort(403, 'Anda tidak diizinkan melihat surat ini.');
            }
        }

        $suratKeluar->load(['user.role', 'status', 'sifat', 'templateSurat', 'persetujuan.penyetuju']);
        return response()->json($suratKeluar); // Mengembalikan JSON untuk modal
    }

    /**
     * Memperbarui surat keluar (oleh mahasiswa/Dosen).
     */
    public function update(Request $request, SuratKeluar $suratKeluar)
    {
        $user = Auth::user();

        // Otorisasi: Hanya pemilik (mahasiswa/Dosen) yang dapat mengedit
        // DAN status surat harus 'Draf' atau 'Ditolak'.
        if (
            $user->id !== $suratKeluar->user_id ||
            !in_array($suratKeluar->status->nama_status, ['Draf', 'Ditolak'])
        ) {
            abort(403, 'Anda tidak diizinkan memperbarui surat ini.');
        }

        $request->validate([
            'perihal' => 'required|string|max:255',
            'penerima' => 'required|string|max:255',
            'sifat_surat_id' => 'required|exists:sifat_surats,id',
            'template_surat_id' => 'nullable|exists:template_surats,id',
            'isi_surat_manual' => 'nullable|string',
            'data_placeholder' => 'nullable|array',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $isiSuratFinal = $request->isi_surat_manual;
        if ($request->filled('template_surat_id')) {
            $template = TemplateSurat::findOrFail($request->template_surat_id);
            $isiSuratFinal = $template->isi_template;
            if ($request->has('data_placeholder') && is_array($request->data_placeholder)) {
                foreach ($request->data_placeholder as $key => $value) {
                    $isiSuratFinal = Str::replace("{{{$key}}}", $value, $isiSuratFinal);
                }
            }
            $isiSuratFinal = preg_replace('/\{\{[^}]+\}\}/', '', $isiSuratFinal);
        }

        $lampiranPath = $suratKeluar->lampiran;
        if ($request->hasFile('lampiran')) {
            if ($suratKeluar->lampiran && Storage::disk('public')->exists($suratKeluar->lampiran)) {
                Storage::disk('public')->delete($suratKeluar->lampiran);
            }
            $lampiranPath = $request->file('lampiran')->store('lampiran_surat_keluar', 'public');
        }

        $statusMenungguPersetujuan = StatusSurat::where('nama_status', 'Menunggu Persetujuan')->firstOrFail();

        $suratKeluar->update([
            'perihal' => $request->perihal,
            'penerima' => $request->penerima,
            'isi_surat' => $isiSuratFinal,
            'lampiran' => $lampiranPath,
            'status_id' => $statusMenungguPersetujuan->id, // Diajukan kembali untuk persetujuan
            'sifat_surat_id' => $request->sifat_surat_id,
            'template_surat_id' => $request->template_surat_id,
        ]);

        return response()->json(['success' => 'Surat berhasil diperbarui dan diajukan kembali.']);
    }

    /**
     * Menghapus surat keluar (oleh mahasiswa/Dosen).
     */
    public function destroy(SuratKeluar $suratKeluar)
    {
        $user = Auth::user();

        // Otorisasi: Hanya pemilik (mahasiswa/Dosen) yang dapat menghapus
        // DAN status surat harus 'Draf' atau 'Ditolak'.
        if (
            $user->id !== $suratKeluar->user_id ||
            !in_array($suratKeluar->status->nama_status, ['Draf', 'Ditolak'])
        ) {
            abort(403, 'Anda tidak diizinkan menghapus surat ini.');
        }

        if ($suratKeluar->lampiran && Storage::disk('public')->exists($suratKeluar->lampiran)) {
            Storage::disk('public')->delete($suratKeluar->lampiran);
        }

        $suratKeluar->delete();
        return response()->json(['success' => 'Surat berhasil dihapus.']);
    }

    /**
     * Endpoint AJAX untuk mendapatkan detail template surat (digunakan oleh form).
     */
    public function getTemplateDetails(Request $request)
    {
        $templateId = $request->input('template_id');
        $template = TemplateSurat::find($templateId);

        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $template->isi_template, $matches);
        $placeholders = $matches[1];

        return response()->json([
            'isi_template' => $template->isi_template,
            'placeholders' => $placeholders
        ]);
    }
    // --- Metode Spesifik untuk staff ---

    /**
     * Menampilkan form untuk proses penomoran surat keluar oleh staff (via modal).
     */
    public function showProcessFormStaffTU(SuratKeluar $suratKeluar)
    {
        if (!Auth::user()->role || Auth::user()->role->name !== 'staff') {
            abort(403, 'Akses ditolak.');
        }

        // Hanya surat dengan status 'Menunggu Persetujuan', 'Draf', atau 'Ditolak' yang bisa diproses nomornya
        if (!in_array($suratKeluar->status->nama_status, ['Menunggu Persetujuan', 'Draf', 'Ditolak'])) {
            return response()->json(['error' => 'Surat ini tidak bisa diproses penomorannya.'], 403);
        }
        return response()->json($suratKeluar); // Mengembalikan data surat untuk mengisi modal
    }

    /**
     * Memproses surat keluar oleh staff (menambahkan nomor dan tanggal surat).
     */
    public function processStaffTU(Request $request, SuratKeluar $suratKeluar)
    {
        if (!Auth::user()->role || Auth::user()->role->name !== 'staff') {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'nomor_surat' => ['required', 'string', Rule::unique('surat_keluars', 'nomor_surat')->ignore($suratKeluar->id)],
            'tanggal_surat' => 'required|date',
        ]);

        $statusMenungguPersetujuan = StatusSurat::where('nama_status', 'Menunggu Persetujuan')->firstOrFail();

        $suratKeluar->update([
            'nomor_surat' => $request->nomor_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'status_id' => $statusMenungguPersetujuan->id, // Status tetap "Menunggu Persetujuan" untuk Pimpinan
        ]);

        // Opsional: Catat aksi Staff TU di riwayat persetujuan
        PersetujuanSuratKeluar::create([
            'surat_keluar_id' => $suratKeluar->id,
            'user_id_penyetuju' => Auth::id(), // ID staff
            'status_persetujuan' => 'Diproses TU',
            'catatan' => 'Surat telah diberi nomor dan diteruskan ke pimpinan untuk persetujuan akhir.',
            'tanggal_persetujuan' => Carbon::now(),
        ]);

        SuratMasuk::create([
            'user_id' => $suratKeluar->user_id, // ID pengguna yang mengajukan
            'perihal' => 'Surat Keluar: ' . $suratKeluar->perihal,
            'isi_surat' => $suratKeluar->isi_surat,
            'nomor_surat' => $suratKeluar->nomor_surat,
            'tanggal_surat' => $suratKeluar->tanggal_surat,
            'nomor_agenda' => 'Surat Keluar',
            'sifat_surat_id' => $suratKeluar->sifat_surat_id,
            'template_surat_id' => $suratKeluar->template_surat_id,
            'lampiran' => $suratKeluar->lampiran,
            'penerima_id' => $suratKeluar->penerima_id, // Penerima surat keluar
            'pengirim_id' => Auth::id(), // ID staff yang memproses
            'tanggal_terima' => Carbon::now(),
            'tanggal_surat' => $suratKeluar->tanggal_surat,
            'status_id' => StatusSurat::where('nama_status', 'Proses')->first()->id, // Status surat masuk
        ]);

        return redirect()->route('staff.surat-keluar.index')->with('success', 'Nomor surat berhasil diproses dan diteruskan ke pimpinan.');
    }

    /**
     * Menampilkan form untuk menolak surat keluar oleh staff (via modal).
     */
    public function showRejectFormStaffTU(SuratKeluar $suratKeluar)
    {
        if (!Auth::user()->role || Auth::user()->role->name !== 'staff') {
            abort(403, 'Akses ditolak.');
        }
        // Hanya surat dengan status 'Menunggu Persetujuan' atau 'Draf' yang bisa ditolak oleh Staff TU
        if (!in_array($suratKeluar->status->nama_status, ['Menunggu Persetujuan', 'Draf'])) {
            return response()->json(['error' => 'Surat ini tidak dalam status untuk ditolak oleh Staff TU.'], 403);
        }
        return response()->json($suratKeluar); // Mengembalikan data surat untuk mengisi modal
    }

    /**
     * Menolak surat keluar oleh staff.
     */
    public function rejectByStaffTU(Request $request, SuratKeluar $suratKeluar)
    {
        if (!Auth::user()->role || Auth::user()->role->name !== 'staff') {
            abort(403, 'Akses ditolak.');
        }

        if (!in_array($suratKeluar->status->nama_status, ['Menunggu Persetujuan', 'Draf'])) {
            return response()->json(['error' => 'Surat ini tidak dalam status untuk ditolak oleh Staff TU.'], 403);
        }

        $request->validate([
            'catatan_penolakan' => 'required|string|min:10',
        ]);

        $statusDitolak = StatusSurat::where('nama_status', 'Ditolak')->firstOrFail();

        $suratKeluar->update([
            'status_id' => $statusDitolak->id,
            'nomor_surat' => null, // Reset nomor surat jika ditolak
            'tanggal_surat' => null, // Reset tanggal surat jika ditolak
        ]);

        PersetujuanSuratKeluar::create([
            'surat_keluar_id' => $suratKeluar->id,
            'user_id_penyetuju' => Auth::id(), // ID staff yang menolak
            'status_persetujuan' => 'Ditolak oleh Staff TU',
            'catatan' => $request->catatan_penolakan,
            'tanggal_persetujuan' => Carbon::now(),
        ]);

        return redirect()->route('staff.surat-keluar.index')->with('Surat berhasil ditolak oleh Staff TU.');
    }


    // --- Metode Spesifik untuk Pimpinan ---

    /**
     * Menampilkan form untuk menyetujui/menolak surat oleh Pimpinan (via modal).
     */
    public function showApproveFormPimpinan(SuratKeluar $suratKeluar)
    {
        if (!Auth::user()->role || Auth::user()->role->name !== 'Pimpinan') {
            abort(403, 'Akses ditolak.');
        }
        // Hanya surat dengan status 'Menunggu Persetujuan' yang bisa diproses Pimpinan
        if ($suratKeluar->status->nama_status !== 'Menunggu Persetujuan') {
            return response()->json(['error' => 'Surat ini tidak dalam status menunggu persetujuan.'], 403);
        }
        return response()->json($suratKeluar); // Mengembalikan data surat untuk mengisi modal
    }

    /**
     * Menyetujui surat keluar oleh Pimpinan.
     */
    public function approvePimpinan(Request $request, SuratKeluar $suratKeluar)
    {
        if (!Auth::user()->role || Auth::user()->role->name !== 'Pimpinan') {
            abort(403, 'Akses ditolak.');
        }

        if ($suratKeluar->status->nama_status !== 'Menunggu Persetujuan') {
            return response()->json(['error' => 'Surat ini tidak dalam status menunggu persetujuan.'], 403);
        }

        $request->validate([
            'catatan_persetujuan' => 'nullable|string',
        ]);

        $statusDisetujui = StatusSurat::where('nama_status', 'Disetujui')->firstOrFail();

        $suratKeluar->update([
            'status_id' => $statusDisetujui->id,
        ]);

        PersetujuanSuratKeluar::create([
            'surat_keluar_id' => $suratKeluar->id,
            'user_id_penyetuju' => Auth::id(), // ID Pimpinan yang menyetujui
            'status_persetujuan' => 'Disetujui',
            'catatan' => $request->catatan_persetujuan,
            'tanggal_persetujuan' => Carbon::now(),
        ]);

        return response()->json(['success' => 'Surat berhasil disetujui.']);
    }

    /**
     * Menolak surat keluar oleh Pimpinan.
     */
    public function rejectPimpinan(Request $request, SuratKeluar $suratKeluar)
    {
        if (!Auth::user()->role || Auth::user()->role->name !== 'Pimpinan') {
            abort(403, 'Akses ditolak.');
        }

        if ($suratKeluar->status->nama_status !== 'Menunggu Persetujuan') {
            return response()->json(['error' => 'Surat ini tidak dalam status menunggu persetujuan.'], 403);
        }

        $request->validate([
            'catatan_penolakan' => 'required|string|min:10',
        ]);

        $statusDitolak = StatusSurat::where('nama_status', 'Ditolak')->firstOrFail();

        $suratKeluar->update([
            'status_id' => $statusDitolak->id,
            'nomor_surat' => null, // Reset nomor surat jika ditolak
            'tanggal_surat' => null, // Reset tanggal surat jika ditolak
        ]);

        PersetujuanSuratKeluar::create([
            'surat_keluar_id' => $suratKeluar->id,
            'user_id_penyetuju' => Auth::id(), // ID Pimpinan yang menolak
            'status_persetujuan' => 'Ditolak',
            'catatan' => $request->catatan_penolakan,
            'tanggal_persetujuan' => Carbon::now(),
        ]);

        return response()->json(['success' => 'Surat berhasil ditolak.']);
    }

    /**
     * Menghasilkan PDF dari surat keluar yang sudah disetujui.
     */
    public function generatePdf(SuratKeluar $suratKeluar)
    {
        // Otorisasi: Hanya Pimpinan, staff, Admin yang bisa mengunduh PDF surat yang sudah disetujui
        if (!Auth::user()->role || !in_array(Auth::user()->role->name, ['pimpinan', 'staff', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        if ($suratKeluar->status->nama_status !== 'Disetujui' || !$suratKeluar->nomor_surat) {
            return redirect()->back()->with('error', 'Surat belum disetujui atau belum memiliki nomor surat.');
        }

        try {
            $data = [
                'surat' => $suratKeluar,
                // Konfigurasi ini harus ada di config/app.php atau .env Anda
                'kampus_nama' => config('app.kampus_nama', 'Nama Kampus Anda'),
                'kampus_alamat' => config('app.kampus_alamat', 'Alamat Kampus Anda'),
                'kampus_nama_kota' => config('app.kampus_nama_kota', 'Kota Kampus Anda'),
                // Anda bisa menambahkan data pejabat penanda tangan di sini
                // 'nama_pejabat_penanda_tangan' => 'Prof. Dr. [Nama Rektor]',
                // 'jabatan_pejabat_penanda_tangan' => 'Rektor',
            ];

            $pdf = Pdf::loadView('pdf.surat_keluar', $data);
            $pdf->setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            return $pdf->download('surat-keluar-' . Str::slug($suratKeluar->perihal) . '.pdf');
        } catch (\Exception $e) {
            Log::error('Gagal mendownload PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh PDF: ' . $e->getMessage());
        }
    }
}
