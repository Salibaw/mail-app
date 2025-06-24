<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SifatSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SifatSuratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sifatSurat = SifatSurat::select(['id', 'nama_sifat']);
            return datatables()->of($sifatSurat)
                ->addIndexColumn()
                ->addColumn('action', function ($sifatSurat) {
                    return '
                        <button onclick="openEditModal(' . $sifatSurat->id . ', \'' . addslashes($sifatSurat->nama) . '\')"
                                class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="' . route('admin.sifat-surat.destroy', $sifatSurat->id) . '" method="POST" class="delete-form inline" onsubmit="return false;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.sifat_surat');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_sifat' => 'required|string|max:255|unique:sifat_surats,nama_sifat',
        ], [
            'nama_sifat.required' => 'Nama sifat surat wajib diisi.',
            'nama_sifat.max' => 'Nama sifat surat maksimum 255 karakter.',
            'nama_sifat.unique' => 'Nama sifat surat sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Gagal menambah sifat surat. Periksa input Anda.');
        }

        SifatSurat::create([
            'nama_sifat' => $request->nama_sifat,
        ]);

        return redirect()->back()->with('success', 'Sifat surat berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SifatSurat $sifatSurat)
    {
        $validator = Validator::make($request->all(), [
            'nama_sifat' => 'required|string|max:255|unique:sifat_surats,nama_sifat,' . $sifatSurat->id,
        ], [
            'nama_sifat.required' => 'Nama sifat surat wajib diisi.',
            'nama_sifat.max' => 'Nama sifat surat maksimum 255 karakter.',
            'nama_sifat.unique' => 'Nama sifat surat sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Gagal memperbarui sifat surat. Periksa input Anda.');
        }

        $sifatSurat->update([
            'nama_sifat' => $request->nama_sifat,
        ]);

        return redirect()->back()->with('success', 'Sifat surat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SifatSurat $sifatSurat)
    {
        try {
            $sifatSurat->delete();
            return redirect()->back()->with('success', 'Sifat surat berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus sifat surat: mungkin terkait dengan data lain.');
        }
    }
}