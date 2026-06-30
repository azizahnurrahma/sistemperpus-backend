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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('id_notification');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->unsignedBigInteger('id_transaksi')->nullable();
            $table->string('title');
            $table->text('message');
            $table->string('type'); // approval, rejection, fine, due_warning
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Foreign key to peminjamans (optional but good practice)
            $table->foreign('id_transaksi')->references('id_transaksi')->on('peminjamans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
