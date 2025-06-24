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
        Schema::create('disposisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuks')->onDelete('cascade');
            $table->foreignId('dari_user_id')->constrained('users')->onDelete('restrict'); // Pemberi disposisi
            $table->foreignId('ke_user_id')->constrained('users')->onDelete('restrict'); // Penerima disposisi
            $table->text('instruksi')->nullable();
            $table->timestamp('tanggal_disposisi')->useCurrent();
            $table->string('status_disposisi')->default('Diteruskan'); // Contoh: Diteruskan, Diterima, Selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisis');
    }
};
