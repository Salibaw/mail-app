<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\SuratKeluar;
use App\Models\StatusSurat;
use App\Models\PersetujuanSuratKeluar;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


class SuratKeluarController extends Controller
{
    /**
     * Display a listing of outgoing letters requiring approval or already approved/rejected.
     */
    public function index()
    {
        $userId = Auth::id(); // ID pimpinan saat ini

        $suratKeluar = SuratKeluar::where('penerima_id', $userId)
            ->whereHas('status', function ($query) {
                $query->whereIn('nama_status', ['Menunggu Persetujuan', 'Disetujui', 'Ditolak','Proses']);
            })
            ->with(['user.role', 'status', 'sifat', 'persetujuan.penyetuju'])
            ->latest()
            ->paginate(10);

        return view('pimpinan.surat_keluar.index', compact('suratKeluar'));
    }

    /**
     * Display the specified outgoing letter.
     */
    public function show(SuratKeluar $suratKeluar)
    {
        $suratKeluar->load(['user.role', 'status', 'sifat', 'templateSurat', 'persetujuan.penyetuju']);
        return view('pimpinan.surat_keluar.show', compact('suratKeluar'));
    }

    /**
     * Approve the specified outgoing letter.
     */
    public function approve(Request $request, SuratKeluar $suratKeluar, SuratMasuk $suratMasuk)
    {
        // Pastikan surat memang statusnya 'Menunggu Persetujuan'
        if ($suratKeluar->status->nama_status !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Surat ini tidak dalam status menunggu persetujuan.');
        }

        $request->validate([
            'catatan_persetujuan' => 'nullable|string',
        ]);

        $statusDisetujui = StatusSurat::where('nama_status', 'Disetujui')->firstOrFail();

        $suratKeluar->update([
            'status_id' => $statusDisetujui->id,
            // Jika nomor surat dan tanggal surat belum ada (misal alur tanpa staf TU pre-fill),
            // maka bisa diisi di sini atau dipastikan sudah ada dari proses staf TU.
            // Untuk saat ini, asumsikan sudah diisi staf TU.
            // 'nomor_surat' => $suratKeluar->nomor_surat ?? 'GEN_NOMOR_OTOMATIS',
            // 'tanggal_surat' => $suratKeluar->tanggal_surat ?? Carbon::now()->toDateString(),
        ]);
        $suratMasuk->update([
            'status_id' => $statusDisetujui->id, // Update status surat masuk terkait
        ]);

        PersetujuanSuratKeluar::create([    
            'surat_keluar_id' => $suratKeluar->id,
            'user_id_penyetuju' => Auth::id(), // ID Pimpinan yang menyetujui
            'status_persetujuan' => 'Disetujui',
            'catatan' => $request->catatan_persetujuan,
            'tanggal_persetujuan' => Carbon::now(),
        ]);

        return redirect()->route('pimpinan.surat-keluar.index')->with('success', 'Surat berhasil disetujui.');
    }

    /**
     * Reject the specified outgoing letter.
     */
    public function reject(Request $request, SuratKeluar $surat_keluar, SuratMasuk $suratMasuk)
    {
        // Pastikan surat memang statusnya 'Menunggu Persetujuan'
        // if ($suratKeluar->status->nama_status !== 'Menunggu Persetujuan') {
        //     return redirect()->back()->with('error', 'Surat ini tidak dalam status menunggu persetujuan.');
        // }

        $request->validate([
            'catatan_penolakan' => 'required|string',
        ]);

        $statusDitolak = StatusSurat::where('nama_status', 'Ditolak')->firstOrFail();

        $surat_keluar->update([
            'status_id' => $statusDitolak->id,
        ]);
        $suratMasuk->update([
            'status_id' => $statusDitolak->id, 
        ]);

        PersetujuanSuratKeluar::create([
            'surat_keluar_id' => $surat_keluar->id,
            'user_id_penyetuju' => Auth::id(), 
            'status_persetujuan' => 'Ditolak',
            'catatan' => $request->catatan_penolakan,
            'tanggal_persetujuan' => Carbon::now(),
        ]);

        return redirect()->route('pimpinan.surat-keluar.index')->with('success', 'Surat berhasil ditolak.');
    }
    public function download(SuratKeluar $suratKeluar)
    {

        if (!$suratKeluar->status || $suratKeluar->status->nama_status !== 'Disetujui') {
            return redirect()->route('pimpinan.surat-keluar.index')->with('error', 'Hanya surat dengan status Disetujui yang dapat diunduh.');
        }

        try {
            $pdf = Pdf::loadView('pdf.surat_keluar', [
                'suratKeluar' => $suratKeluar,
                'watermark' => 'Dokumen Resmi - ' . now()->format('Y-m-d'),
            ]);
            $pdf->setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            return $pdf->download('surat-keluar-' . ($suratKeluar->nomor_surat ?? 'document') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Gagal mendownload PDF: ' . $e->getMessage());
            return redirect()->route('pimpinan.surat-keluar.index')->with('error', 'Gagal mengunduh PDF.');
        }
    }
}
