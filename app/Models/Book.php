<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    protected $primaryKey = 'id_buku';
    
    protected $fillable = [
        'judul', 'pengarang', 'penerbit', 'tahun_terbit', 'stok', 'id_kategori', 'id_rak'
    ];
}