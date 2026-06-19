<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Book;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    // TAMPILKAN SEMUA PEMINJAMAN
    public function index(Request $request)
    {
        $user = $request->user();

        // Jika admin, tampilkan semua peminjaman
        if ($user->role === 'admin') {
            $peminjamans = Peminjaman::all();
        } else {
            // Jika mahasiswa, hanya tampilkan miliknya sendiri
            $peminjamans = Peminjaman::where('id_user', $user->id_user)->get();
        }

        return response()->json(['status' => 'success', 'data' => $peminjamans], 200);
    }

    // TAMPILKAN DETAIL PEMINJAMAN
    public function show($id)
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json(['status' => 'fail', 'message' => 'Peminjaman tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $peminjaman], 200);
    }

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
            'status' => 'menunggu',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Peminjaman berhasil diajukan, menunggu persetujuan admin.',
            'data' => $peminjaman
        ], 201);
    }

    // RIWAYAT PEMINJAMAN SAYA
    public function riwayat(Request $request)
    {
        $riwayat = Peminjaman::where('id_user', $request->user()->id_user)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $riwayat], 200);
    }

    // PINJAMAN AKTIF
    public function pinjamanAktif(Request $request, $id)
    {
        $aktif = Peminjaman::where('id_user', $request->user()->id_user)
            ->whereIn('status', ['dipinjam', 'disetujui'])
            ->get();

        return response()->json(['status' => 'success', 'data' => $aktif], 200);
    }

    // ADMIN: SETUJUI PEMINJAMAN
    public function setujui($id)
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json(['status' => 'fail', 'message' => 'Peminjaman tidak ditemukan'], 404);
        }

        $peminjaman->update(['status' => 'disetujui']);

        // Kurangi stok buku
        $buku = Book::find($peminjaman->id_buku);
        if ($buku) {
            $buku->decrement('stok');
        }

        return response()->json(['status' => 'success', 'message' => 'Peminjaman disetujui'], 200);
    }

    // ADMIN: TOLAK PEMINJAMAN
    public function tolak($id)
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json(['status' => 'fail', 'message' => 'Peminjaman tidak ditemukan'], 404);
        }

        $peminjaman->update(['status' => 'ditolak']);

        return response()->json(['status' => 'success', 'message' => 'Peminjaman ditolak'], 200);
    }

    // ADMIN: KEMBALIKAN BUKU
    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json(['status' => 'fail', 'message' => 'Peminjaman tidak ditemukan'], 404);
        }

        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_dikembalikan' => date('Y-m-d'),
        ]);

        // Tambah stok buku kembali
        $buku = Book::find($peminjaman->id_buku);
        if ($buku) {
            $buku->increment('stok');
        }

        // Cek apakah terlambat, jika ya buat denda
        $tanggalKembali = strtotime($peminjaman->tanggal_kembali);
        $tanggalHariIni = strtotime(date('Y-m-d'));

        if ($tanggalHariIni > $tanggalKembali) {
            $selisihHari = ($tanggalHariIni - $tanggalKembali) / (60 * 60 * 24);
            $jumlahDenda = $selisihHari * 1000; // Rp 1.000 per hari

            \DB::table('dendas')->insert([
                'id_transaksi' => $peminjaman->id_transaksi,
                'id_user' => $peminjaman->id_user,
                'jumlah_denda' => $jumlahDenda,
                'status_bayar' => 'belum_bayar',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Buku berhasil dikembalikan'], 200);
    }
}