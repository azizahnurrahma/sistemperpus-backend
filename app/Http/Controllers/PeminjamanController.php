<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Book; // Pastikan nama model Buku kamu sesuai (Book atau Buku)
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    // Fungsi untuk memproses Mahasiswa meminjam buku
    public function pinjamBuku(Request $request)
    {
        // 1. Validasi inputan dari Postman
        $this->validate($request, [
            'id_buku' => 'required|exists:books,id_buku',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        // 2. Ambil id_user secara otomatis dari orang yang sedang Login (lewat Token)
        $idUserYangLogin = $request->user()->id_user;

        // 3. Cek apakah stok buku masih ada
        $buku = Book::find($request->id_buku);
        if ($buku->stok <= 0) {
            return response()->json(['message' => 'Maaf, stok buku ini sudah habis!'], 400);
        }

        // 4. Simpan data peminjaman ke database
        $peminjaman = Peminjaman::create([
            'id_user' => $idUserYangLogin,
            'id_buku' => $request->id_buku,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => 'dipinjam',
        ]);

        // 5. Kurangi stok buku sebanyak 1
        $buku->decrement('stok');

        return response()->json([
            'message' => 'Buku Berhasil Dipinjam! Selamat Membaca.',
            'data' => $peminjaman
        ], 201);
    }
}