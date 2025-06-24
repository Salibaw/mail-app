<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SifatSurat extends Model
{
    use HasFactory;

    protected $table = 'sifat_surats';
    protected $fillable = ['nama_sifat'];

    /**
     * Get the incoming letters with this nature.
     */
    public function suratMasuk(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'sifat_surat_id');
    }

    /**
     * Get the outgoing letters with this nature.
     */
    public function suratKeluar(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'sifat_surat_id');
    }
}