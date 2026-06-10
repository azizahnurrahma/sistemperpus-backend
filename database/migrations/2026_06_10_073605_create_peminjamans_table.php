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
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id('id_transaksi'); 
        
            // Relasi Foreign Key: id_user (dari tabel users) dan id_buku (dari tabel books)
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_buku')->constrained('books', 'id_buku')->onDelete('cascade');
            
            // Kolom pelengkap transaksi sirkulasi perpustakaan
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->string('status')->default('dipinjam'); // Nilai awal langsung 'dipinjam'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
