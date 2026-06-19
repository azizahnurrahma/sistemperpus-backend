<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Book; 
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    // Fungsi untuk memproses Mahasiswa meminjam buku
    public function pinjamBuku(Request $request)
    {
        // === 1. SATPAM HAK AKSES: Hanya Mahasiswa yang boleh pinjam ===
        if (auth()->user()->role !== 'mahasiswa') {
            return response()->json([
                'status' => 'fail',
                'message' => 'Akses Ditolak! Hanya Mahasiswa yang dapat meminjam buku.'
            ], 403);
        }

        // 2. Validasi inputan dari Postman
        $this->validate($request, [
            'id_buku' => 'required|exists:books,id_buku',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        // 3. Ambil id_user secara otomatis dari orang yang sedang Login (lewat Token)
        $idUserYangLogin = $request->user()->id_user;

        // 4. Cek apakah stok buku masih ada
        $buku = Book::find($request->id_buku);
        if ($buku->stok <= 0) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Maaf, stok buku ini sudah habis!'
            ], 400);
        }

        // 5. Simpan data peminjaman ke database
        $peminjaman = Peminjaman::create([
            'id_user' => $idUserYangLogin,
            'id_buku' => $request->id_buku,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => 'dipinjam',
        ]);

        // 6. Kurangi stok buku sebanyak 1 (Jurus andalan kamu!)
        $buku->decrement('stok');

        return response()->json([
            'status' => 'success',
            'message' => 'Buku Berhasil Dipinjam! Selamat Membaca.',
            'data' => $peminjaman
        ], 201);
    }
}