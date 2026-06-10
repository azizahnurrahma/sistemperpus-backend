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
        // 1. Matikan pengecekan relasi sementara biar ga diblokir MySQL
        \Schema::disableForeignKeyConstraints();

        // 2. Panggil seeder prodi kita
        $this->call(ProdiSeeder::class);

        // 3. Hidupkan kembali pengecekan relasinya biar database tetap aman
        \Schema::enableForeignKeyConstraints();
    }
}