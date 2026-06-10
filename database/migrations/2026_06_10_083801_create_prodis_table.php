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
        Schema::create('prodis', function (Blueprint $table) {
            $table->id('id_prodi'); // Primary key prodi sesuai laporan
            $table->string('nama_prodi'); // Nama program studi (misal: Informatika, Sistem Informasi)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
