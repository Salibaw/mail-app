<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $role = Role::where('name', $row['role'])->first();
        if (!$role) {
            throw new \Exception('Role ' . $row['role'] . ' tidak ditemukan.');
        }

        return new User([
            'nama' => $row['nama'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'nip_nim' => $row['nip_nim'] ?? null,
            'telepon' => $row['telepon'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'role_id' => $role->id,
        ]);
    }
}