<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TemplateController extends Controller
{
    /**
     * Display a listing of the templates for DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $templates = TemplateSurat::with('user')->select(['id', 'nama_template', 'jenis_surat', 'isi_template', 'user_id']);

            return DataTables::of($templates)
                ->addIndexColumn()
                ->addColumn('user.nama', function ($template) {
                    return $template->user ? $template->user->name : '-';
                })
                ->addColumn('action', function ($template) {
    $escapedIsi = htmlspecialchars($template->isi_template, ENT_QUOTES);
    return '
        <button onclick="openEditModal(' . $template->id . ', \'' . addslashes($template->nama_template) . '\', \'' . addslashes($template->jenis_surat) . '\', \'' . $escapedIsi . '\')" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
            <i class="fas fa-edit"></i> Edit
        </button>
        <form action="' . route('admin.templates.destroy', $template->id) . '" method="POST" class="delete-form inline-block">
            ' . csrf_field() . '
            ' . method_field('DELETE') . '
            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </form>';
})
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.template');
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'jenis_surat' => 'required|string|max:255',
            'isi_template' => 'required|string',
        ]);

        TemplateSurat::create([
            'nama_template' => $request->nama_template,
            'jenis_surat' => $request->jenis_surat,
            'isi_template' => $request->isi_template,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil dibuat.');
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, TemplateSurat $template)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'jenis_surat' => 'required|string|max:255',
            'isi_template' => 'required|string',
        ]);

        $template->update([
            'nama_template' => $request->nama_template,
            'jenis_surat' => $request->jenis_surat,
            'isi_template' => $request->isi_template,
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil diperbarui.');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(TemplateSurat $template)
    {
        $template->delete();
        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil dihapus.');
    }
}
