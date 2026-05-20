<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use Illuminate\Support\Facades\Validator;
// use App\Models\Kategori;
class KategoriController extends Controller
{


// TugasController.php atau KategoriController.php
public function getKategori(Request $request)
{
    // Mengambil kategori milik user yang sedang login
    $kategori = \App\Models\Kategori::where('id_user', $request->user()->id)->get();

    return response()->json([
        'success' => true,
        'data'    => $kategori
    ], 200);
}
    /**
     * Menampilkan semua data kategori
     */
   public function index(Request $request)
{
    // Ambil kategori milik user yang sedang login saja
    $kategori = kategori::where('id_user', $request->user()->id)->get();

    return response()->json([
        'success' => true,
        'message' => 'List Data Kategori',
        'data'    => $kategori
    ], 200);
}

    /**
     * Menyimpan data kategori baru
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nama_kategori' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi Gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // Simpan ke database
    $kategori = Kategori::create([
        'nama_kategori' => $request->nama_kategori,
        'id_user'       => $request->user()->id, // <--- AMBIL ID USER DARI TOKEN OTOMATIS
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Kategori Berhasil Disimpan',
        'data'    => $kategori
    ], 201);
}

    /**
     * Menampilkan detail satu kategori
     */
    public function show($id)
    {
        $kategori = Kategori::find($id);

        if ($kategori) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Data Kategori',
                'data'    => $kategori
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kategori Tidak Ditemukan',
        ], 404);
    }

    /**
     * Mengupdate data kategori
     */
    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Tidak Ditemukan',
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Update data
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori Berhasil Diupdate',
            'data'    => $kategori
        ], 200);
    }

    /**
     * Menghapus data kategori
     */
    public function destroy($id)
    {
        $kategori = kategori::find($id);

        if ($kategori) {
            $kategori->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori Berhasil Dihapus',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kategori Tidak Ditemukan',
        ], 404);
    }

}
