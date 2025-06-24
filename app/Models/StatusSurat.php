<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusSurat extends Model
{
    use HasFactory;

    protected $table = 'status_surats';
    protected $fillable = ['nama_status'];

    /**
     * Get the incoming letters with this status.
     */
    public function suratMasuk(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'status_id');
    }

    /**
     * Get the outgoing letters with this status.
     */
    public function suratKeluar(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'status_id');
    }
}