<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class TugasController extends Controller
{

   public function getTugas(Request $request)
{
    $now = now(); // Mengambil waktu server saat ini (WIB/sesuai config)

    $tugas = Tugas::with('kategori')
        ->where('id_user', $request->user()->id)
        ->get()
        ->map(function ($item) use ($now) {
            // LOGIKA: Jika waktu server sudah melewati tanggal tugas 
            // DAN tugas belum selesai (is_done masih 0)
            if ($item->tanggal < $now && !$item->is_done) {
                // Jika di DB masih 0, maka update jadi 1
                if (!$item->its_over) {
                    $item->update(['its_over' => true]);
                }
            } else {
                // Jika ternyata tanggal diubah jadi masa depan, kembalikan ke 0
                if ($item->its_over) {
                    $item->update(['its_over' => false]);
                }
            }
            return $item;
        });

    return response()->json([
        'success' => true,
        'data' => $tugas
    ]);
}
    // TugasController.php
        public function updateStatus(Request $request, $id)
        {
            $tugas = Tugas::findOrFail($id);
            
            // Update kolom is_done sesuai migrasi
            $tugas->is_done = $request->is_done; 
            $tugas->save();

            return response()->json(['success' => true]);
        }

    public function CreateTugas(Request $request)
    {
        // 1. Manual Validation agar detail error bisa dikustomisasi
        $validator = Validator::make($request->all(), [
            'id_kategori' => 'required|exists:kategori,id',
            'tanggal'     => 'required|date',
            'prioritas'   => 'required|in:ya,tidak',
            'tugas'       => 'required|string|max:255',
        ], [
            // Custom Message (Opsional)
            'id_kategori.exists' => 'Kategori tidak ditemukan di database.',
            'prioritas.in'       => 'Prioritas harus bernilai "ya" atau "tidak".',
        ]);

        // Jika Validasi Gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors() // Menampilkan detail field mana yang salah
            ], 422);
        }

        try {
            // 2. Simpan ke Database
            $tugas = Tugas::create([
                'id_user'     => $request->user()->id,
                'id_kategori' => $request->id_kategori,
                'tanggal'     => $request->tanggal,
                'prioritas'   => $request->prioritas,
                'tugas'       => $request->tugas,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil dibuat',
                'data'    => $tugas
            ], 201);

        } catch (Exception $e) {
            // 3. Menangani error sistem (seperti database mati atau kolom kurang)
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error_detail' => $e->getMessage() // Menampilkan pesan error teknis
            ], 500);
        }

        
    }
    /**
     * Ambil detail satu tugas berdasarkan ID (Show)
     */
    public function show(Request $request, $id)
    {
        try {
            // Mengambil tugas milik user yang sedang login dengan relasi kategori
            $tugas = Tugas::with('kategori')
                ->where('id_user', $request->user()->id)
                ->find($id);

            if (!$tugas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tugas tidak ditemukan atau Anda tidak memiliki akses.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $tugas
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail tugas.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui data tugas (Edit/Update)
     */
    public function update(Request $request, $id)
    {
        // 1. Cari data tugas
        $tugas = Tugas::where('id_user', $request->user()->id)->find($id);

        if (!$tugas) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'id_kategori' => 'required|exists:kategori,id',
            'tanggal'     => 'required|date',
            'prioritas'   => 'required|in:ya,tidak',
            'tugas'       => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // 3. Update data
            $tugas->update([
                'id_kategori' => $request->id_kategori,
                'tanggal'     => $request->tanggal,
                'prioritas'   => $request->prioritas,
                'tugas'       => $request->tugas,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil diperbarui',
                'data'    => $tugas
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tugas',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus tugas (Delete)
     */
    public function destroy(Request $request, $id)
    {
        try {
            $tugas = Tugas::where('id_user', $request->user()->id)->find($id);

            if (!$tugas) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            $tugas->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil dihapus'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tugas',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}