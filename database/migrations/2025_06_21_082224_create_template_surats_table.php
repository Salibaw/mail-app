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
        Schema::create('template_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template')->unique();
            $table->text('isi_template'); // Berisi placeholder seperti {{nama}}, {{nim}}, dll.
            $table->string('jenis_surat'); // Contoh: Surat Keterangan, Surat Permohonan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pembuat template
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_surats');
    }
};
