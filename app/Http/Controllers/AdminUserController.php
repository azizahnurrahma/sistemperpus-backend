<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    // Menampilkan daftar seluruh mahasiswa
    public function index()
    {
        $mahasiswas = \DB::table('users')
            ->join('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->leftJoin('prodis', 'mahasiswas.id_prodi', '=', 'prodis.id_prodi')
            ->where('users.role', 'mahasiswa')
            ->select(
                'users.id_user',
                'users.email',
                'users.role',
                'mahasiswas.nim',
                'mahasiswas.nama',
                'prodis.nama_prodi'
            )
            ->get();

        return response()->json(['status' => 'success', 'data' => $mahasiswas], 200);
    }

    // Menampilkan detail mahasiswa
    public function show($id)
    {
        $mahasiswa = \DB::table('users')
            ->join('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
            ->leftJoin('prodis', 'mahasiswas.id_prodi', '=', 'prodis.id_prodi')
            ->where('users.id_user', $id)
            ->select(
                'users.id_user',
                'users.email',
                'users.role',
                'mahasiswas.nim',
                'mahasiswas.nama',
                'prodis.nama_prodi',
                'prodis.id_prodi'
            )
            ->first();

        if (!$mahasiswa) {
            return response()->json(['status' => 'fail', 'message' => 'Mahasiswa tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $mahasiswa], 200);
    }

    // Menambahkan data mahasiswa (sama seperti registerMahasiswa)
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nama' => 'required|string|max:255',
            'nim' => 'required|string',
            'id_prodi' => 'required|integer'
        ]);

        $user = User::create([
            'email' => $request->input('email'),
            'password' => app('hash')->make($request->input('password')),
            'role' => 'mahasiswa'
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
            'status' => 'success',
            'message' => 'Mahasiswa berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    // Memperbarui data mahasiswa
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => 'fail', 'message' => 'User tidak ditemukan'], 404);
        }

        // Update email jika ada
        if ($request->has('email')) {
            $user->update(['email' => $request->input('email')]);
        }

        // Update password jika ada
        if ($request->has('password')) {
            $user->update(['password' => app('hash')->make($request->input('password'))]);
        }

        // Update data mahasiswa
        $updateData = [];
        if ($request->has('nama')) $updateData['nama'] = $request->input('nama');
        if ($request->has('nim')) $updateData['nim'] = $request->input('nim');
        if ($request->has('id_prodi')) $updateData['id_prodi'] = $request->input('id_prodi');

        if (!empty($updateData)) {
            $updateData['updated_at'] = date('Y-m-d H:i:s');
            \DB::table('mahasiswas')->where('id_user', $id)->update($updateData);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data mahasiswa berhasil diperbarui'
        ], 200);
    }

    // Menghapus data mahasiswa
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => 'fail', 'message' => 'User tidak ditemukan'], 404);
        }

        // Hapus data mahasiswa dulu (foreign key constraint)
        \DB::table('mahasiswas')->where('id_user', $id)->delete();
        // Hapus user
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Mahasiswa berhasil dihapus'
        ], 200);
    }
}
