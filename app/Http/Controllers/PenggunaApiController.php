<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Pengguna;

class PenggunaApiController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        $penggunas = Pengguna::all();
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $penggunas
        ]);
    }

    // Menampilkan detail pengguna berdasarkan ID
    public function show($id)
    {
        $pengguna = Pengguna::find($id);

        if ($pengguna) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $pengguna
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }
    }

    // Menambahkan pengguna baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'username' => 'required|string|unique:pengguna,username',
            'no_hp' => 'required|string|max:20|unique:pengguna,no_hp',
            'password' => 'required|string|min:8',
            'level' => 'required|string|in:Owner,Kepala Produksi,Customer Service',
            'otp' => 'required|integer|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pengguna = Pengguna::create([
            'id_pengguna' => Pengguna::generateId(),
            'nama' => $request->nama,
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'level' => $request->level,
            'otp' => $request->otp,
        ]);

        return response()->json([
            'code' => 201,
            'status' => 'success',
            'data' => $pengguna
        ], 201);
    }

    // Memperbarui data pengguna
    public function update(Request $request, $id_pengguna)
    {
        $pengguna = Pengguna::find($id_pengguna);

        if ($pengguna) {
            $pengguna->update($request->all());

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Berhasil memperbarui data pengguna',
                'data' => $pengguna
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }
    }

    // Menghapus pengguna
    public function destroy($id_pengguna)
    {
        $pengguna = Pengguna::find($id_pengguna);

        if ($pengguna) {
            $pengguna->delete();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Pengguna berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }
    }
}
