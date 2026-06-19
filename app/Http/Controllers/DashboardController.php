<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Book;
use App\Models\User;

class DashboardController extends Controller
{
    // DASHBOARD ADMIN: Menampilkan statistik
    public function admin()
    {
        $totalBuku = Book::count();
        $totalUser = User::where('role', 'mahasiswa')->count();
        $totalPeminjaman = Peminjaman::count();
        $peminjamanAktif = Peminjaman::whereIn('status', ['dipinjam', 'disetujui'])->count();
        $peminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $totalDenda = \DB::table('dendas')->where('status_bayar', 'belum_bayar')->count();
        $bukuTersedia = Book::where('stok', '>', 0)->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_buku' => $totalBuku,
                'buku_tersedia' => $bukuTersedia,
                'total_mahasiswa' => $totalUser,
                'total_peminjaman' => $totalPeminjaman,
                'peminjaman_aktif' => $peminjamanAktif,
                'peminjaman_menunggu' => $peminjamanMenunggu,
                'total_denda_belum_bayar' => $totalDenda,
            ]
        ], 200);
    }

    // DASHBOARD MAHASISWA: Menampilkan ringkasan data mahasiswa yang login
    public function mahasiswa(Request $request)
    {
        $userId = $request->user()->id_user;

        $totalPinjaman = Peminjaman::where('id_user', $userId)->count();
        $pinjamanAktif = Peminjaman::where('id_user', $userId)
            ->whereIn('status', ['dipinjam', 'disetujui'])
            ->count();
        $pinjamanMenunggu = Peminjaman::where('id_user', $userId)
            ->where('status', 'menunggu')
            ->count();
        $totalDenda = \DB::table('dendas')
            ->where('id_user', $userId)
            ->where('status_bayar', 'belum_bayar')
            ->sum('jumlah_denda');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_pinjaman' => $totalPinjaman,
                'pinjaman_aktif' => $pinjamanAktif,
                'pinjaman_menunggu' => $pinjamanMenunggu,
                'total_denda_belum_bayar' => $totalDenda,
            ]
        ], 200);
    }
}
