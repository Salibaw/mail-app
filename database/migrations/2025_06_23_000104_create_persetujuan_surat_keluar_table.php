<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('persetujuan_surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_keluar_id')->constrained('surat_keluar')->onDelete('cascade');
            $table->foreignId('user_id_penyetuju')->constrained('users')->onDelete('restrict'); // Pimpinan/User yang menyetujui
            $table->string('status_persetujuan')->default('Menunggu'); // Contoh: Menunggu, Disetujui, Ditolak
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan_surat_keluar');
    }
};
