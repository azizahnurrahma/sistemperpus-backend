<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    // Nama tabel di database
    protected $table = 'books';

    // 🛑 PERBAIKAN 1: Kembalikan primary key ke ID Buku (bukan rak!)
    // Sesuaikan dengan nama kolom primary key di migration buku kamu (id_buku atau id)
    protected $primaryKey = 'id_buku'; 

    // 🛑 PERBAIKAN 2: Ganti id_rak menjadi rak agar bisa menyimpan teks bebas dari Postman
    protected $fillable = [
        'judul', 
        'pengarang', 
        'penerbit', 
        'tahun_terbit', 
        'stok', 
        'id_kategori', 
        'rak' 
    ];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'id_buku');
    }
}