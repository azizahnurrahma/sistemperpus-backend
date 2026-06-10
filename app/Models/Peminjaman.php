<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    // Memberitahu Lumen bahwa tabelnya bernama peminjamans
    protected $table = 'peminjamans';

    // Memberitahu nama Primary Key-nya sesuai laporan
    protected $primaryKey = 'id_transaksi';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'id_user',
        'id_buku',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status'
    ];
}