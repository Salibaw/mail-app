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
         Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_agenda')->unique();
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->date('tanggal_terima');
            $table->foreignId('pengirim_id')->constrained('users')->onDelete('restrict'); // Pengirim surat, bisa dari tabel pengirim_surats
            $table->string('perihal');
            $table->text('isi_ringkas')->nullable();
            $table->string('lampiran')->nullable(); // Path ke file lampiran

            // Foreign Keys
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Pencatat surat
            $table->foreignId('status_id')->constrained('status_surats')->onDelete('restrict');
            $table->foreignId('sifat_surat_id')->constrained('sifat_surats')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuks');
    }
};
