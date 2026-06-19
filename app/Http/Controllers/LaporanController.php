<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Book;
use App\Models\User;

class LaporanController extends Controller
{
    // Laporan Peminjaman
    public function peminjaman(Request $request)
    {
        $query = \DB::table('peminjamans')
            ->join('users', 'peminjamans.id_user', '=', 'users.id_user')
            ->leftJoin('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->join('books', 'peminjamans.id_buku', '=', 'books.id_buku')
            ->select(
                'peminjamans.*',
                'mahasiswas.nama',
                'mahasiswas.nim',
                'books.judul as judul_buku'
            );

        // Filter berdasarkan tanggal jika ada
        if ($request->has('dari')) {
            $query->where('peminjamans.tanggal_pinjam', '>=', $request->dari);
        }
        if ($request->has('sampai')) {
            $query->where('peminjamans.tanggal_pinjam', '<=', $request->sampai);
        }

        $data = $query->orderBy('peminjamans.created_at', 'desc')->get();

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    // Laporan Pengembalian
    public function pengembalian(Request $request)
    {
        $query = \DB::table('peminjamans')
            ->join('users', 'peminjamans.id_user', '=', 'users.id_user')
            ->leftJoin('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->join('books', 'peminjamans.id_buku', '=', 'books.id_buku')
            ->where('peminjamans.status', 'dikembalikan')
            ->select(
                'peminjamans.*',
                'mahasiswas.nama',
                'mahasiswas.nim',
                'books.judul as judul_buku'
            );

        if ($request->has('dari')) {
            $query->where('peminjamans.tanggal_kembali', '>=', $request->dari);
        }
        if ($request->has('sampai')) {
            $query->where('peminjamans.tanggal_kembali', '<=', $request->sampai);
        }

        $data = $query->orderBy('peminjamans.updated_at', 'desc')->get();

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    // Laporan Denda
    public function denda(Request $request)
    {
        $query = \DB::table('dendas')
            ->join('users', 'dendas.id_user', '=', 'users.id_user')
            ->leftJoin('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->leftJoin('peminjamans', 'dendas.id_transaksi', '=', 'peminjamans.id_transaksi')
            ->leftJoin('books', 'peminjamans.id_buku', '=', 'books.id_buku')
            ->select(
                'dendas.*',
                'mahasiswas.nama',
                'mahasiswas.nim',
                'books.judul as judul_buku'
            );

        if ($request->has('status_bayar')) {
            $query->where('dendas.status_bayar', $request->status_bayar);
        }

        $data = $query->orderBy('dendas.created_at', 'desc')->get();

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    // Laporan Stok Buku
    public function buku()
    {
        $data = \DB::table('books')
            ->leftJoin('kategoris', 'books.id_kategori', '=', 'kategoris.id_kategori')
            ->select('books.*', 'kategoris.nama_kategori')
            ->orderBy('books.judul', 'asc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    // Laporan Data Mahasiswa
    public function pengguna()
    {
        $data = \DB::table('users')
            ->join('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->leftJoin('prodis', 'mahasiswas.id_prodi', '=', 'prodis.id_prodi')
            ->where('users.role', 'mahasiswa')
            ->select(
                'users.id_user',
                'users.email',
                'mahasiswas.nim',
                'mahasiswas.nama',
                'prodis.nama_prodi'
            )
            ->orderBy('mahasiswas.nama', 'asc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    // Export ke PDF
    public function exportPdf(Request $request)
    {
        // Tentukan jenis laporan dari parameter
        $jenis = $request->query('jenis', 'peminjaman');

        switch ($jenis) {
            case 'peminjaman':
                $data = $this->getDataPeminjaman($request);
                break;
            case 'pengembalian':
                $data = $this->getDataPengembalian($request);
                break;
            case 'denda':
                $data = $this->getDataDenda($request);
                break;
            case 'buku':
                $data = $this->getDataBuku();
                break;
            case 'pengguna':
                $data = $this->getDataPengguna();
                break;
            default:
                $data = [];
        }

        // Karena Lumen tidak punya built-in PDF generator,
        // kita return data JSON dengan header yang menandakan laporan
        // Untuk implementasi PDF penuh, install package seperti dompdf/dompdf
        return response()->json([
            'status' => 'success',
            'message' => 'Data laporan ' . $jenis . ' siap untuk di-export ke PDF',
            'jenis_laporan' => $jenis,
            'data' => $data
        ], 200);
    }

    // Export ke Excel
    public function exportExcel(Request $request)
    {
        $jenis = $request->query('jenis', 'peminjaman');

        switch ($jenis) {
            case 'peminjaman':
                $data = $this->getDataPeminjaman($request);
                break;
            case 'pengembalian':
                $data = $this->getDataPengembalian($request);
                break;
            case 'denda':
                $data = $this->getDataDenda($request);
                break;
            case 'buku':
                $data = $this->getDataBuku();
                break;
            case 'pengguna':
                $data = $this->getDataPengguna();
                break;
            default:
                $data = [];
        }

        // Untuk implementasi Excel penuh, install package seperti maatwebsite/excel
        return response()->json([
            'status' => 'success',
            'message' => 'Data laporan ' . $jenis . ' siap untuk di-export ke Excel',
            'jenis_laporan' => $jenis,
            'data' => $data
        ], 200);
    }

    // === HELPER METHODS (Private) ===

    private function getDataPeminjaman($request)
    {
        $query = \DB::table('peminjamans')
            ->join('users', 'peminjamans.id_user', '=', 'users.id_user')
            ->leftJoin('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->join('books', 'peminjamans.id_buku', '=', 'books.id_buku')
            ->select('peminjamans.*', 'mahasiswas.nama', 'mahasiswas.nim', 'books.judul as judul_buku');

        if ($request->has('dari')) $query->where('peminjamans.tanggal_pinjam', '>=', $request->dari);
        if ($request->has('sampai')) $query->where('peminjamans.tanggal_pinjam', '<=', $request->sampai);

        return $query->orderBy('peminjamans.created_at', 'desc')->get();
    }

    private function getDataPengembalian($request)
    {
        $query = \DB::table('peminjamans')
            ->join('users', 'peminjamans.id_user', '=', 'users.id_user')
            ->leftJoin('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->join('books', 'peminjamans.id_buku', '=', 'books.id_buku')
            ->where('peminjamans.status', 'dikembalikan')
            ->select('peminjamans.*', 'mahasiswas.nama', 'mahasiswas.nim', 'books.judul as judul_buku');

        if ($request->has('dari')) $query->where('peminjamans.tanggal_kembali', '>=', $request->dari);
        if ($request->has('sampai')) $query->where('peminjamans.tanggal_kembali', '<=', $request->sampai);

        return $query->orderBy('peminjamans.updated_at', 'desc')->get();
    }

    private function getDataDenda($request)
    {
        $query = \DB::table('dendas')
            ->join('users', 'dendas.id_user', '=', 'users.id_user')
            ->leftJoin('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->leftJoin('peminjamans', 'dendas.id_transaksi', '=', 'peminjamans.id_transaksi')
            ->leftJoin('books', 'peminjamans.id_buku', '=', 'books.id_buku')
            ->select('dendas.*', 'mahasiswas.nama', 'mahasiswas.nim', 'books.judul as judul_buku');

        if ($request->has('status_bayar')) $query->where('dendas.status_bayar', $request->status_bayar);

        return $query->orderBy('dendas.created_at', 'desc')->get();
    }

    private function getDataBuku()
    {
        return \DB::table('books')
            ->leftJoin('kategoris', 'books.id_kategori', '=', 'kategoris.id_kategori')
            ->select('books.*', 'kategoris.nama_kategori')
            ->orderBy('books.judul', 'asc')
            ->get();
    }

    private function getDataPengguna()
    {
        return \DB::table('users')
            ->join('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->leftJoin('prodis', 'mahasiswas.id_prodi', '=', 'prodis.id_prodi')
            ->where('users.role', 'mahasiswa')
            ->select('users.id_user', 'users.email', 'mahasiswas.nim', 'mahasiswas.nama', 'prodis.nama_prodi')
            ->orderBy('mahasiswas.nama', 'asc')
            ->get();
    }
}
