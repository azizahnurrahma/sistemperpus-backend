<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // Menampilkan daftar kategori buku
    public function index()
    {
        $kategoris = \DB::table('kategoris')->get();

        return response()->json(['status' => 'success', 'data' => $kategoris], 200);
    }
}
