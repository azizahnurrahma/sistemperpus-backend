<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    // 1. TAMPILKAN SEMUA BUKU (Get All)
    public function index()
    {
        $books = Book::all();
        return response()->json([
            'status' => 'success',
            'data' => $books
        ], 200);
    }

    // 2. TAMBAH BUKU BARU (Create)
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

    // 3. LIHAT DETAIL SATU BUKU (Get Detail)
    public function show($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['status' => 'fail', 'message' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $book], 200);
    }

    // 4. UBAH DATA BUKU (Update)
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

    // 5. HAPUS BUKU (Delete)
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
}