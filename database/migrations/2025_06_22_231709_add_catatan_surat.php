<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->text('catatan_surat')->nullable()->after('lampiran');
        });

        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->text('catatan_surat')->nullable()->after('lampiran');
        });
    }

    public function down()
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropColumn('catatan_surat');
        });

        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->dropColumn('catatan_surat');
        });
    }
};
