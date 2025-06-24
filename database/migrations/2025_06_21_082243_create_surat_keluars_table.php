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
        Schema::create('surat_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique()->nullable(); // Akan terisi setelah disetujui
            $table->date('tanggal_surat')->nullable(); // Akan terisi setelah disetujui
            $table->string('perihal');
            $table->foreignId('penerima_id')->constrained('users')->onDelete('restrict'); // Penerima surat, bisa dari tabel penerima_surats
            $table->text('isi_surat')->nullable(); // Bisa diisi manual atau dari template
            $table->string('lampiran')->nullable(); // Path ke file lampiran

            // Foreign Keys
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Pengaju/pembuat surat
            $table->foreignId('status_id')->constrained('status_surats')->onDelete('restrict');
            $table->foreignId('sifat_surat_id')->constrained('sifat_surats')->onDelete('restrict');
            $table->foreignId('template_surat_id')->nullable()->constrained('template_surats')->onDelete('set null'); // Opsional, jika dari template

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluars');
    }
};
