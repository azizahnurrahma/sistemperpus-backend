<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DendaController extends Controller
{
    // ADMIN: Menampilkan daftar seluruh denda
    public function index()
    {
        $dendas = \DB::table('dendas')
            ->join('users', 'dendas.id_user', '=', 'users.id_user')
            ->leftJoin('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->select(
                'dendas.*',
                'mahasiswas.nama',
                'mahasiswas.nim',
                'users.email'
            )
            ->get();

        return response()->json(['status' => 'success', 'data' => $dendas], 200);
    }

    // Menampilkan detail denda
    public function show($id)
    {
        $denda = \DB::table('dendas')
            ->join('users', 'dendas.id_user', '=', 'users.id_user')
            ->leftJoin('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->leftJoin('peminjamans', 'dendas.id_transaksi', '=', 'peminjamans.id_transaksi')
            ->leftJoin('books', 'peminjamans.id_buku', '=', 'books.id_buku')
            ->where('dendas.id_denda', $id)
            ->select(
                'dendas.*',
                'mahasiswas.nama',
                'mahasiswas.nim',
                'users.email',
                'books.judul as judul_buku'
            )
            ->first();

        if (!$denda) {
            return response()->json(['status' => 'fail', 'message' => 'Denda tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $denda], 200);
    }

    // ADMIN: Mengubah status pembayaran denda
    public function bayar($id)
    {
        $denda = \DB::table('dendas')->where('id_denda', $id)->first();

        if (!$denda) {
            return response()->json(['status' => 'fail', 'message' => 'Denda tidak ditemukan'], 404);
        }

        \DB::table('dendas')->where('id_denda', $id)->update([
            'status_bayar' => 'sudah_bayar',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status denda berhasil diubah menjadi sudah dibayar'
        ], 200);
    }

    // Menampilkan daftar denda milik pengguna sendiri
    public function saya(Request $request)
    {
        $userId = $request->user()->id_user;

        $dendas = \DB::table('dendas')
            ->leftJoin('peminjamans', 'dendas.id_transaksi', '=', 'peminjamans.id_transaksi')
            ->leftJoin('books', 'peminjamans.id_buku', '=', 'books.id_buku')
            ->where('dendas.id_user', $userId)
            ->select(
                'dendas.*',
                'books.judul as judul_buku'
            )
            ->get();

        return response()->json(['status' => 'success', 'data' => $dendas], 200);
    }
}
