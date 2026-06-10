<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // 3. Cek user & password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Email atau password salah!'
            ], 401);
        }

        // 4. Buat Token baru
        $token = Str::random(40);
        $user->update(['api_token' => $token]);

        // 5. Respon sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }
    // 1. FUNGSI REGISTER ADMIN (BEBAS AKSES DI LUAR)
    public function registerAdmin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nama' => 'required|string|max:255',
            'nip' => 'required|string' 
        ]);

        $user = \App\Models\User::create([
            'email' => $request->input('email'),
            'password' => app('hash')->make($request->input('password')),
            'role' => 'admin' // Otomatis diset sebagai admin
        ]);

        \DB::table('admins')->insert([
            'nip' => $request->input('nip'),
            'nama' => $request->input('nama'),
            'id_user' => $user->id_user, 
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'message' => 'Registrasi Admin Berhasil!',
            'user' => $user
        ], 201);
    }

    // 2. FUNGSI REGISTER MAHASISWA (WAJIB ADMIN YANG LOGIN)
    public function registerMahasiswa(Request $request)
    {
        // SATPAM: Cek apakah user yang sedang pencet tombol ini rolenya Admin?
        $yangLogin = auth()->user();
        if ($yangLogin->role !== 'admin') {
            return response()->json([
                'message' => 'Akses Ditolak! Hanya Admin yang boleh mendaftarkan Mahasiswa.'
            ], 403); // 403 Forbidden
        }

        // Jika lolos satpam, jalankan validasi & simpan data mahasiswa
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nama' => 'required|string|max:255',
            'nim' => 'required|string',
            'id_prodi' => 'required|integer'
        ]);

        $user = \App\Models\User::create([
            'email' => $request->input('email'),
            'password' => app('hash')->make($request->input('password')),
            'role' => 'mahasiswa' // Otomatis diset sebagai mahasiswa
        ]);

        \DB::table('mahasiswas')->insert([
            'nim' => $request->input('nim'),
            'nama' => $request->input('nama'),
            'id_prodi' => $request->input('id_prodi'),
            'id_user' => $user->id_user, 
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'message' => 'Mahasiswa Baru Berhasil Didaftarkan oleh Admin!',
            'user' => $user
        ], 201);
    }

    public function getProfile(Request $request)
    {
        // Auth::user() otomatis mengambil data user yang sedang login berdasarkan token
        $user = auth()->user(); 

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan atau belum login'], 401);
        }

        // Jika rolenya mahasiswa, JOIN ke tabel mahasiswas dan prodis
        if ($user->role === 'mahasiswa') {
            $dataProfile = \DB::table('users')
                ->join('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
                ->leftJoin('prodis', 'mahasiswas.id_prodi', '=', 'prodis.id_prodi')
                ->where('users.id_user', $user->id_user)
                ->select('users.id_user', 'users.email', 'users.role', 'mahasiswas.nim', 'mahasiswas.nama', 'prodis.nama_prodi')
                ->first();
        } 
        // Jika rolenya admin, JOIN ke tabel admins
        else {
            $dataProfile = \DB::table('users')
                ->join('admins', 'users.id_user', '=', 'admins.id_user')
                ->where('users.id_user', $user->id_user)
                ->select('users.id_user', 'users.email', 'users.role', 'admins.nip', 'admins.nama')
                ->first();
        }

        return response()->json([
            'data' => $dataProfile
        ], 200);
    }
    public function logout(Request $request)
    {
        // 1. Ambil data user yang sedang login berdasarkan tokennya
        $user = auth()->user();

        if ($user) {
            // 2. Set kolom api_token menjadi null di database biar tokennya hangus
            $user->api_token = null;
            $user->save();

            return response()->json([
                'message' => 'Logout Berhasil! Token Anda telah dihapus dari sistem.'
            ], 200);
        }

        return response()->json([
            'message' => 'Gagal logout, user tidak dikenali.'
        ], 401);
    }
}