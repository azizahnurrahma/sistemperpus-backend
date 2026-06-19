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
        Schema::create('dendas', function (Blueprint $table) {
            $table->id('id_denda');
            $table->foreignId('id_transaksi')->constrained('peminjamans', 'id_transaksi')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->integer('jumlah_denda');
            $table->string('status_bayar')->default('belum_bayar'); // belum_bayar / sudah_bayar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dendas');
    }
};
