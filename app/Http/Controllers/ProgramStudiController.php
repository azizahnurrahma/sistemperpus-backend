<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    // Menampilkan daftar program studi
    public function index()
    {
        $prodis = \DB::table('prodis')->get();

        return response()->json(['status' => 'success', 'data' => $prodis], 200);
    }

    // Menampilkan detail program studi
    public function show($id)
    {
        $prodi = \DB::table('prodis')->where('id_prodi', $id)->first();

        if (!$prodi) {
            return response()->json(['status' => 'fail', 'message' => 'Program studi tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $prodi], 200);
    }
}
