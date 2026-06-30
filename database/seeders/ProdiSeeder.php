<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdiSeeder extends Seeder
{
    public function run()
    {
        // Bersihkan tabel prodi dulu agar data tidak menumpuk/double saat di-run ulang
        DB::table('prodis')->delete();

        // Mengisi pilihan prodi otomatis ke database (tepat 5 prodi unik)
        DB::table('prodis')->insert([
            [
                'nama_prodi' => 'Teknik Informatika',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_prodi' => 'Manajemen',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_prodi' => 'Akuntansi',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_prodi' => 'Hukum',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_prodi' => 'Sistem Informasi',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}