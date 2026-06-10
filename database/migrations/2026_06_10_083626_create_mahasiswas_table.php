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
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->string('nim')->primary(); // NIM sebagai primary key sesuai laporan
            $table->string('nama');
            
            // JANGAN pakai ->constrained() dulu biar ga eror urutan tabelnya
            $table->integer('id_prodi'); 

            // Relasi ke users tetap aman karena tabel users dibuat paling awal
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
