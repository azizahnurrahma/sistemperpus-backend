<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // DASHBOARD ADMIN: Menampilkan statistik & tabel persetujuan
    public function admin()
    {
        $totalBuku = Book::count();
        $totalUser = User::where('role', 'mahasiswa')->count();
        
        // Sesuai UI: "Buku Sedang Dipinjam"
        $bukuSedangDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        
        // Sesuai UI: "Denda Belum Dibayar (14 User)" -> Menghitung jumlah USER unik yang punya denda
        $userDendaBelumBayar = \DB::table('dendas')
            ->where('status_bayar', 'belum_bayar')
            ->distinct('id_user')
            ->count('id_user');

        // Sesuai UI: Tabel "Menunggu Persetujuan" / "Menunggu Diambil"
        // Pastikan model Peminjaman sudah memiliki relasi 'user' dan 'book'
        $menungguPersetujuan = Peminjaman::where('status', 'menunggu') 
            ->with(['user', 'book']) 
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stat_cards' => [
                    'total_buku' => $totalBuku,
                    'total_mahasiswa' => $totalUser,
                    'buku_sedang_dipinjam' => $bukuSedangDipinjam,
                    'user_belum_bayar_denda' => $userDendaBelumBayar,
                ],
                'menunggu_persetujuan' => $menungguPersetujuan
            ]
        ], 200);
    }

    // DASHBOARD MAHASISWA: Menampilkan data personal, jadwal, dan rekomendasi buku
    public function mahasiswa(Request $request)
    {
        $user = $request->user();

        // 🛑 TRAMPILAN BARU: SATPAM HAK AKSES (Mencegah Admin masuk)
        if ($user->role !== 'mahasiswa') {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak! Halaman dashboard ini hanya khusus untuk Mahasiswa.'
            ], 403);
        }

        $userId = $user->id_user; // Sesuaikan jika primary key Anda 'id'

        
        // 1. Jadwal Pengembalian (Buku yang sedang dipinjam saat ini)
        $jadwalPengembalian = Peminjaman::where('id_user', $userId)
            ->where('status', 'dipinjam')
            ->with('book')
            ->get();

        // 2. Target Bulan Ini (Contoh: Menghitung buku yang sukses dikembalikan bulan ini)
        $bukuSelesaiBulanIni = Peminjaman::where('id_user', $userId)
            ->where('status', 'dikembalikan') // Sesuaikan status selesai Anda
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();
            
        $targetBuku = 6; // Sesuai di UI targetnya 6 buku

        // 3. Buku Baru di Teknik Informatika
        // Catatan: Jika ada sistem filter prodi, silakan tambahkan ->where('kategori/prodi', 'Teknik Informatika')
        $bukuBaru = Book::latest()->take(4)->get();

        // 4. Sering Dipinjam Mahasiswa (Buku Terpopuler)
        // Memerlukan relasi 'peminjamans' di dalam Model Book
        $seringDipinjam = Book::withCount('peminjamans')
            ->orderBy('peminjamans_count', 'desc')
            ->take(4)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_info' => [
                    'nama' => $user->nama,
                    'prodi' => $user->prodi ?? 'Teknik Informatika', // Fallback jika belum ada field prodi
                ],
                'jadwal_pengembalian' => $jadwalPengembalian,
                'target_bulan_ini' => [
                    'selesai' => $bukuSelesaiBulanIni,
                    'target' => $targetBuku,
                    'persentase' => $targetBuku > 0 ? round(($bukuSelesaiBulanIni / $targetBuku) * 100) : 0
                ],
                'buku_baru' => $bukuBaru,
                'sering_dipinjam' => $seringDipinjam
            ]
        ], 200);
    }
}