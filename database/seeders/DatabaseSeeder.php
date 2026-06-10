<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Bersihkan data user lama dulu agar tidak duplikat saat dijalankan ulang
        User::truncate();

        // 2. Tambah data dummy Akun Admin (akun kamu)
        User::create([
            'nama' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'), // Otomatis di-hash/enkripsi aman
            'role' => 'admin'
        ]);

        // 3. Tambah data dummy Akun Mahasiswa (buat tes login mahasiswa nanti)
        User::create([
            'nama' => 'Budi Sudarsono',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('mahasiswa123'),
            'role' => 'mahasiswa'
        ]);

        $this->command->info('Data user berhasil ditambahkan!');
    }
}