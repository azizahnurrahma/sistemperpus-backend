<?php

namespace Database\Seeders; // Sesuaikan namespace dengan seeder bawaan Lumen kamu

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Bersihkan tabel kategori dulu biar data nggak double saat di-run ulang
        DB::table('kategoris')->truncate(); // Sesuaikan nama tabel kategorimu (misal: kategoris / categories)

        // 2. Masukkan data kategori bawaan perpustakaan kampus
        $kategori = [
            ['nama_kategori' => 'Teknik Informatika'],
            ['nama_kategori' => 'Sistem Informasi'],
            ['nama_kategori' => 'Sains & Matematika'],
            ['nama_kategori' => 'Ekonomi & Bisnis'],
            ['nama_kategori' => 'Novel & Fiksi'],
            ['nama_kategori' => 'Jurnal & Karya Ilmiah'],
        ];

        // 3. Eksekusi simpan ke database
        DB::table('kategoris')->insert($kategori);
    }
}