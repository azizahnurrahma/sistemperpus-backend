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
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kategori')->nullable()->after('stok');
            
            // 🛑 UBAH DI SINI: Dari unsignedBigInteger id_rak menjadi string rak
            $table->string('rak', 50)->nullable()->after('id_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // 🛑 UBAH DI SINI: Sesuaikan nama kolom yang di-drop saat rollback
            $table->dropColumn(['id_kategori', 'rak']);
        });
    }
};