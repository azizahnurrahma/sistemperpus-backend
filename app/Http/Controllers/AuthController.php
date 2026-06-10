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
}