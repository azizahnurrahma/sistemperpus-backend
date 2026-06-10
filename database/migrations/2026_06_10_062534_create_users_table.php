<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique(); // Supaya email tidak bisa kembar
            $table->string('password');
            $table->enum('role', ['admin', 'mahasiswa']); // Hak akses sesuai laporan
            $table->string('api_token', 80)->nullable(); // Tiket autentikasi login
            $table->timestamps(); // Otomatis membuat kolom created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}