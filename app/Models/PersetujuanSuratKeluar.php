<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersetujuanSuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'persetujuan_surat_keluars';
    protected $fillable = [
        'surat_keluar_id',
        'user_id_penyetuju',
        'status_persetujuan',
        'catatan',
        'tanggal_persetujuan',
    ];

    protected $casts = [
        'tanggal_persetujuan' => 'datetime',
    ];

    /**
     * Get the outgoing letter that this approval belongs to.
     */
    public function suratKeluar(): BelongsTo
    {
        return $this->belongsTo(SuratKeluar::class);
    }

    /**
     * Get the user who approved this outgoing letter.
     */
    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_penyetuju');
    }
}