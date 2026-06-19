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
        'tanggal_dikembalikan',
        'status'
    ];

    /**
     * Relasi ke model Book (Satu transaksi peminjaman memiliki satu buku)
     */
    public function book()
    {
        // 'id_buku' dicocokkan dengan foreign key yang ada di $fillable kamu
        return $this->belongsTo(Book::class, 'id_buku');
    }

    /**
     * Relasi ke model User (Satu transaksi peminjaman dimiliki oleh satu user/mahasiswa)
     */
    public function user()
    {
        // 'id_user' dicocokkan dengan foreign key yang ada di $fillable kamu
        return $this->belongsTo(User::class, 'id_user');
    }
}