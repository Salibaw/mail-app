<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disposisi extends Model
{
    use HasFactory;

    protected $table = 'disposisis';
    protected $fillable = [
        'surat_masuk_id',
        'dari_user_id',
        'ke_user_id',
        'instruksi',
        'tanggal_disposisi',
        'status_disposisi',
    ];

    protected $casts = [
        'tanggal_disposisi' => 'datetime',
    ];

    /**
     * Get the incoming letter that this disposition belongs to.
     */
    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class);
    }

    /**
     * Get the user who gave this disposition.
     */
    public function dariUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    /**
     * Get the user who received this disposition.
     */
    public function keUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ke_user_id');
    }
}