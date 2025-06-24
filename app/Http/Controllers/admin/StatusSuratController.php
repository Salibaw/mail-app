<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatusSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusSuratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $statusSurat = StatusSurat::select(['id', 'nama_status']);
            return datatables()->of($statusSurat)
                ->addIndexColumn()
                ->addColumn('action', function ($statusSurat) {
                    return '
                        <button onclick="openEditModal(' . $statusSurat->id . ', \'' . addslashes($statusSurat->nama_status) . '\')"
                                class="text-indigo-600 hover:text-indigo-800 mr-2">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="' . route('admin.status-surat.destroy', $statusSurat->id) . '" method="POST" class="delete-form inline" onsubmit="return false;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.status_surat');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_status' => 'required|string|max:255|unique:status_surats,nama_status',
        ], [
            'nama_status.required' => 'Nama status surat wajib diisi.',
            'nama_status.max' => 'Nama status surat maksimum 255 karakter.',
            'nama_status.unique' => 'Nama status surat sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Gagal menambah status surat. Periksa input Anda.');
        }

        StatusSurat::create([
            'nama_status' => $request->nama_status,
        ]);

        return redirect()->back()->with('success', 'Status surat berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StatusSurat $statusSurat)
    {
        $validator = Validator::make($request->all(), [
            'nama_status' => 'required|string|max:255|unique:status_surats,nama_status,' . $statusSurat->id,
        ], [
            'nama_status.required' => 'Nama status surat wajib diisi.',
            'nama_status.max' => 'Nama status surat maksimum 255 karakter.',
            'nama_status.unique' => 'Nama status surat sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Gagal memperbarui status surat. Periksa input Anda.');
        }

        $statusSurat->update([
            'nama_status' => $request->nama_status,
        ]);

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StatusSurat $statusSurat)
    {
        try {
            $statusSurat->delete();
            return redirect()->back()->with('success', 'Status surat berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus status surat: mungkin terkait dengan data lain.');
        }
    }
}