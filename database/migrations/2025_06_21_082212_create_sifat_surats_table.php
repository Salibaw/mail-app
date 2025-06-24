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
        Schema::create('sifat_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sifat')->unique(); // Contoh: Rahasia, Penting, Segera, Biasa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sifat_surats');
    }
};
