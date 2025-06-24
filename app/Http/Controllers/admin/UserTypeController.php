<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return view('admin.role', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Modal sudah ditangani di Blade, tidak perlu view terpisah
        return redirect()->route('admin.roles.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Role::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Modal sudah ditangani di Blade, tidak perlu view terpisah
        return redirect()->route('admin.roles.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diperbarui.');
    } 

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.roles.index')->with('error', 'Gagal menghapus role: ' . $e->getMessage());
        }
    }
}