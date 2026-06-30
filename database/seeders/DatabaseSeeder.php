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

        // 3. Bersihkan data admin, mahasiswa, dan user lama biar tidak double saat di-seed ulang
        DB::table('dendas')->delete();
        DB::table('peminjamans')->delete();
        DB::table('admins')->delete();
        DB::table('mahasiswas')->delete();
        User::whereIn('role', ['admin', 'mahasiswa'])->delete(); 

        // 4. STEP 1: Bikin akun login admin
        $userAdmin = User::create([
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // 5. STEP 2: Bikin profil lengkap admin
        DB::table('admins')->insert([
            'nip'        => '1234567890',
            'nama'       => 'Admin Perpustakaan',
            'id_user'    => $userAdmin->id_user,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        // 6. Bikin akun login mahasiswa default
        $userMhs = User::create([
            'email'    => 'mahasiswa@gmail.com',
            'password' => Hash::make('mahasiswa123'),
            'role'     => 'mahasiswa',
        ]);

        // 7. Bikin profil lengkap mahasiswa default
        DB::table('mahasiswas')->insert([
            'nim'        => '2201012',
            'nama'       => 'Fani Intan Nuraini',
            'id_prodi'   => 1, // Teknik Informatika
            'id_user'    => $userMhs->id_user,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        // 8. Bersihkan data buku lama dan bikin buku bawaan
        DB::table('books')->delete();
        DB::table('books')->insert([
            [
                'judul'        => 'Python Programming',
                'pengarang'    => 'Martin Evans',
                'penerbit'     => "O'Reilly Media",
                'tahun_terbit' => 2022,
                'stok'         => 10,
                'id_kategori'  => 1, // Teknik Informatika
                'rak'          => 'RAK-IT 101',
                'created_at'   => \Carbon\Carbon::now(),
                'updated_at'   => \Carbon\Carbon::now(),
            ],
            [
                'judul'        => 'Machine Learning For Beginners',
                'pengarang'    => 'Jerry N.P.',
                'penerbit'     => 'Packt Publishing',
                'tahun_terbit' => 2022,
                'stok'         => 12,
                'id_kategori'  => 1, // Teknik Informatika
                'rak'          => 'RAK-IT 102',
                'created_at'   => \Carbon\Carbon::now(),
                'updated_at'   => \Carbon\Carbon::now(),
            ],
            [
                'judul'        => 'Expert C Programming',
                'pengarang'    => 'Peter Van Der Linden',
                'penerbit'     => 'Prentice Hall',
                'tahun_terbit' => 2021,
                'stok'         => 8,
                'id_kategori'  => 1, // Teknik Informatika
                'rak'          => 'RAK-IT 103',
                'created_at'   => \Carbon\Carbon::now(),
                'updated_at'   => \Carbon\Carbon::now(),
            ],
            [
                'judul'        => 'Pengantar Ekonomi Mikro',
                'pengarang'    => 'Sri Rahayu, S.E., M.Si.',
                'penerbit'     => 'Erlangga',
                'tahun_terbit' => 2022,
                'stok'         => 15,
                'id_kategori'  => 4, // Ekonomi & Bisnis
                'rak'          => 'RAK-UMUM 201',
                'created_at'   => \Carbon\Carbon::now(),
                'updated_at'   => \Carbon\Carbon::now(),
            ],
            [
                'judul'        => 'Akuntansi Keuangan Lanjutan',
                'pengarang'    => 'Endah Prawesti Ningrum, S.E, M.Ak',
                'penerbit'     => 'Salemba Empat',
                'tahun_terbit' => 2023,
                'stok'         => 7,
                'id_kategori'  => 2, // Sistem Informasi
                'rak'          => 'RAK-UMUM 202',
                'created_at'   => \Carbon\Carbon::now(),
                'updated_at'   => \Carbon\Carbon::now(),
            ]
        ]);

        $book1 = DB::table('books')->where('judul', 'Python Programming')->first();
        $book2 = DB::table('books')->where('judul', 'Expert C Programming')->first();

        // 9. Bikin transaksi terlambat dikembalikan & kena denda (Sudah Dikembalikan tapi telat)
        $idTransaksi1 = DB::table('peminjamans')->insertGetId([
            'id_user' => $userMhs->id_user,
            'id_buku' => $book1->id_buku,
            'tanggal_pinjam' => '2026-06-01',
            'tanggal_kembali' => '2026-06-08',
            'tanggal_dikembalikan' => '2026-06-15',
            'status' => 'dikembalikan',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('dendas')->insert([
            'id_transaksi' => $idTransaksi1,
            'id_user' => $userMhs->id_user,
            'jumlah_denda' => 7000, // 7 hari terlambat @ Rp 1.000
            'status_bayar' => 'belum_bayar',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        // 10. Bikin transaksi sedang dipinjam dan statusnya terlambat (Belum Dikembalikan dan sudah lewat tanggal kembali)
        DB::table('peminjamans')->insert([
            'id_user' => $userMhs->id_user,
            'id_buku' => $book2->id_buku,
            'tanggal_pinjam' => '2026-06-10',
            'tanggal_kembali' => '2026-06-17', // Melewati batas hari ini (26 Juni)
            'tanggal_dikembalikan' => null,
            'status' => 'dipinjam',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        // 11. Bikin 5 data denda dari mahasiswa yang berbeda
        $additionalStudents = [
            [
                'email' => 'andi@gmail.com',
                'nim' => '2201013',
                'nama' => 'Andi Wijaya',
                'id_prodi' => 2, // Manajemen
                'judul_buku' => 'Python Programming',
                'tanggal_pinjam' => '2026-06-02',
                'tanggal_kembali' => '2026-06-09',
                'tanggal_dikembalikan' => '2026-06-14', // Terlambat 5 hari
                'jumlah_denda' => 5000,
            ],
            [
                'email' => 'budi@gmail.com',
                'nim' => '2201014',
                'nama' => 'Budi Santoso',
                'id_prodi' => 3, // Akuntansi
                'judul_buku' => 'Machine Learning For Beginners',
                'tanggal_pinjam' => '2026-06-03',
                'tanggal_kembali' => '2026-06-10',
                'tanggal_dikembalikan' => '2026-06-20', // Terlambat 10 hari
                'jumlah_denda' => 10000,
            ],
            [
                'email' => 'citra@gmail.com',
                'nim' => '2201015',
                'nama' => 'Citra Lestari',
                'id_prodi' => 4, // Hukum
                'judul_buku' => 'Pengantar Ekonomi Mikro',
                'tanggal_pinjam' => '2026-06-05',
                'tanggal_kembali' => '2026-06-12',
                'tanggal_dikembalikan' => '2026-06-15', // Terlambat 3 hari
                'jumlah_denda' => 3000,
            ],
            [
                'email' => 'dewi@gmail.com',
                'nim' => '2201016',
                'nama' => 'Dewi Sartika',
                'id_prodi' => 5, // Sistem Informasi
                'judul_buku' => 'Akuntansi Keuangan Lanjutan',
                'tanggal_pinjam' => '2026-06-04',
                'tanggal_kembali' => '2026-06-11',
                'tanggal_dikembalikan' => '2026-06-21', // Terlambat 10 hari
                'jumlah_denda' => 10000,
            ],
            [
                'email' => 'eko@gmail.com',
                'nim' => '2201017',
                'nama' => 'Eko Prasetyo',
                'id_prodi' => 1, // Teknik Informatika
                'judul_buku' => 'Python Programming',
                'tanggal_pinjam' => '2026-06-06',
                'tanggal_kembali' => '2026-06-13',
                'tanggal_dikembalikan' => '2026-06-25', // Terlambat 12 hari
                'jumlah_denda' => 12000,
            ]
        ];

        foreach ($additionalStudents as $studentData) {
            $userObj = User::create([
                'email'    => $studentData['email'],
                'password' => Hash::make('mahasiswa123'),
                'role'     => 'mahasiswa',
            ]);

            DB::table('mahasiswas')->insert([
                'nim'        => $studentData['nim'],
                'nama'       => $studentData['nama'],
                'id_prodi'   => $studentData['id_prodi'],
                'id_user'    => $userObj->id_user,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            $bk = DB::table('books')->where('judul', $studentData['judul_buku'])->first();

            $idTx = DB::table('peminjamans')->insertGetId([
                'id_user' => $userObj->id_user,
                'id_buku' => $bk->id_buku,
                'tanggal_pinjam' => $studentData['tanggal_pinjam'],
                'tanggal_kembali' => $studentData['tanggal_kembali'],
                'tanggal_dikembalikan' => $studentData['tanggal_dikembalikan'],
                'status' => 'dikembalikan',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            DB::table('dendas')->insert([
                'id_transaksi' => $idTx,
                'id_user' => $userObj->id_user,
                'jumlah_denda' => $studentData['jumlah_denda'],
                'status_bayar' => 'belum_bayar',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }

        // 12. Hidupkan kembali pengecekan relasinya
        Schema::enableForeignKeyConstraints();
    }
}