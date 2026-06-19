<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    // 1. TAMPILKAN SEMUA BUKU (Bisa diakses Admin & Mahasiswa)
    public function index()
    {
        $books = Book::all();
        return response()->json([
            'status' => 'success',
            'data' => $books
        ], 200);
    }

    // 2. TAMBAH BUKU BARU (HANYA ADMIN - sudah dilindungi middleware)
    public function store(Request $request)
    {
        $this->validate($request, [
            'judul' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'tahun_terbit' => 'required|numeric',
            'stok' => 'required|numeric'
        ]);

        $book = Book::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil ditambahkan',
            'data' => $book
        ], 201);
    }

    // 3. LIHAT DETAIL SATU BUKU (Bisa diakses Admin & Mahasiswa)
    public function show($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['status' => 'fail', 'message' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $book], 200);
    }

    // 4. UBAH DATA BUKU (HANYA ADMIN - sudah dilindungi middleware)
    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['status' => 'fail', 'message' => 'Buku tidak ditemukan'], 404);
        }

        $book->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil diperbarui',
            'data' => $book
        ], 200);
    }

    // 5. HAPUS BUKU (HANYA ADMIN - sudah dilindungi middleware)
    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['status' => 'fail', 'message' => 'Buku tidak ditemukan'], 404);
        }

        $book->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil dihapus'
        ], 200);
    }

    // 6. BUKU BERDASARKAN KATEGORI
    public function getByKategori($id)
    {
        $books = Book::where('id_kategori', $id)->get();
        return response()->json(['status' => 'success', 'data' => $books], 200);
    }

    // 7. BUKU BERDASARKAN RAK
    public function getByRak($id)
    {
        $books = Book::where('id_rak', $id)->get();
        return response()->json(['status' => 'success', 'data' => $books], 200);
    }

    // 8. CEK STOK BUKU
    public function cekStok($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['status' => 'fail', 'message' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_buku' => $book->id_buku,
                'judul' => $book->judul,
                'stok' => $book->stok
            ]
        ], 200);
    }
}