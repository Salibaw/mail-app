<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'surat_masuks';
    protected $fillable = [
        'nomor_agenda',
        'nomor_surat',
        'tanggal_surat',
        'tanggal_terima',
        'pengirim_id',
        'perihal',
        'isi_ringkas',
        'lampiran',
        'user_id',
        'status_id',
        'sifat_surat_id',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_terima' => 'date',
    ];

    /**
     * Get the user who recorded the incoming letter.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status of the incoming letter.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusSurat::class, 'status_id');
    }

    /**
     * Get the nature of the incoming letter.
     */
    public function sifat(): BelongsTo
    {
        return $this->belongsTo(SifatSurat::class, 'sifat_surat_id');
    }

    /**
     * Get the dispositions for the incoming letter.
     */
    public function disposisi(): HasMany
    {
        return $this->hasMany(Disposisi::class);
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }
}
