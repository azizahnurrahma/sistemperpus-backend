<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Matikan pengecekan relasi sementara
        Schema::disableForeignKeyConstraints();

        // 2. Panggil seeder prodi dan kategori
        $this->call(ProdiSeeder::class);
        $this->call(KategoriSeeder::class);

        // 3. Bersihkan data admin lama biar tidak double saat di-seed ulang
        // Karena ada relasi cascade, menghapus user otomatis menghapus admin-nya juga
        User::where('role', 'admin')->delete(); 

        // 4. STEP 1: Bikin akun login di tabel `users`
        $userAdmin = User::create([
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // 5. STEP 2: Bikin profil lengkap (NIP & Nama) di tabel `admins`
        DB::table('admins')->insert([
            'nip'        => '1234567890',
            'nama'       => 'Admin Perpustakaan',
            'id_user'    => $userAdmin->id_user, // Menghubungkan ke id_user yang di atas
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        // 6. Hidupkan kembali pengecekan relasinya
        Schema::enableForeignKeyConstraints();
    }
}