<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluars';
    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'penerima_id',
        'isi_surat',
        'lampiran',
        'user_id',
        'status_id',
        'sifat_surat_id',
        'template_surat_id',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];


    /**
     * Get the user who created the outgoing letter.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the recipient of the outgoing letter.
     */
    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }


    /**
     * Get the status of the outgoing letter.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusSurat::class, 'status_id');
    }

    /**
     * Get the nature of the outgoing letter.
     */
    public function sifat(): BelongsTo
    {
        return $this->belongsTo(SifatSurat::class, 'sifat_surat_id');
    }

    /**
     * Get the template used for the outgoing letter.
     */
    public function templateSurat(): BelongsTo
    {
        return $this->belongsTo(TemplateSurat::class);
    }

    /**
     * Get the approvals for the outgoing letter.
     */
    public function persetujuan(): HasMany
    {
        return $this->hasMany(PersetujuanSuratKeluar::class);
    }
}
