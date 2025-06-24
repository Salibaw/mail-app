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
        Schema::create('status_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status')->unique(); // Contoh: Draf, Menunggu Persetujuan, Disetujui, Dikirim, Diterima, Didisposisi, Selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_surats');
    }
};
