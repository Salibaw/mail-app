<?php

namespace App\Http\Controllers\Admin;
use Yajra\DataTables\DataTables;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('role')->select(['id', 'nama', 'email', 'nip_nim', 'role_id']);
            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('role', function ($user) {
                    return $user->role ? $user->role->name : '-';
                })
                ->addColumn('action', function ($user) {
                    return '
                        <button onclick="openEditModal(' . $user->id . ', \'' . addslashes($user->nama) . '\', \'' . $user->email . '\', \'' . ($user->nip_nim ?? '') . '\', \'' . ($user->telepon ?? '') . '\', \'' . ($user->alamat ?? '') . '\', ' . ($user->role_id ?? 'null') . ')"
                                class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="' . route('admin.users.destroy', $user->id) . '" method="POST" class="delete-form inline" onsubmit="return false;">
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
        $roles = Role::all();
        return view('admin.user', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'nip_nim' => 'nullable|string|max:255|unique:users,nip_nim',
            'telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'nip_nim.unique' => 'NIP/NIM sudah digunakan.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Gagal menambah user. Periksa input Anda.');
        }

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nip_nim' => $request->nip_nim,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'role_id' => $request->role_id,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'nip_nim' => 'nullable|string|max:255|unique:users,nip_nim,' . $user->id,
            'telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
            'nip_nim.unique' => 'NIP/NIM sudah digunakan.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Gagal memperbarui user. Periksa input Anda.');
        }

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'nip_nim' => $request->nip_nim,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->back()->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus user: mungkin user terkait dengan data lain.');
        }
    }

    /**
     * Import users from Excel/CSV file.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes' => 'File harus berupa Excel atau CSV.',
            'file.max' => 'File maksimum 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('error', 'Gagal mengimpor user. Periksa file Anda.');
        }

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect()->back()->with('success', 'User berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor user: ' . $e->getMessage());
        }
    }
}