<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateSurat extends Model
{
    use HasFactory;

    protected $table = 'template_surats';
    protected $fillable = [
        'nama_template',
        'isi_template',
        'jenis_surat',
        'user_id',
    ];

    /**
     * Get the user that created the template.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the outgoing letters that used this template.
     */
    public function suratKeluar(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'template_surat_id');
    }
}