<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'nip_nim',
        'telepon',
        'alamat',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the user type that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class,'role_id');
    }

    /**
     * Get the outgoing letters received by the user.
     */
    public function suratKeluarDiterima(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'penerima_id');
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }
    /**
     * Get the incoming letters recorded by the user.
     */
    public function suratMasuk(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'user_id');
    }

    /**
     * Get the outgoing letters created by the user.
     */
    public function suratKeluar(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'user_id');
    }

    /**
     * Get the templates created by the user.
     */
    public function templateSurat(): HasMany
    {
        return $this->hasMany(TemplateSurat::class, 'user_id');
    }

    /**
     * Get the dispositions given by the user.
     */
    public function disposisiDiberikan(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'dari_user_id');
    }

    /**
     * Get the dispositions received by the user.
     */
    public function disposisiDiterima(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'ke_user_id');
    }

    /**
     * Get the outgoing letter approvals made by the user.
     */
    public function persetujuanSuratKeluar(): HasMany
    {
        return $this->hasMany(PersetujuanSuratKeluar::class, 'user_id_penyetuju');
    }
}
