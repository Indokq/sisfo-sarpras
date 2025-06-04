<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{

    
    public function store(Request $request)
    {
        // Validasi input dari mobile
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'barang_id' => 'required|exists:barangs,id',
            'nama_peminjam' => 'required|string|max:255',
            'alasan_meminjam' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
            'status' => 'in:pending,approved,rejected,returned',
        ]);

        DB::beginTransaction();

        try {
            // Menyimpan data peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'barang_id' => $request->barang_id,
                'nama_peminjam' => $request->nama_peminjam,
                'alasan_meminjam' => $request->alasan_meminjam,
                'jumlah' => $request->jumlah,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status' => 'pending', 
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil dibuat.',
                'data' => [
                    'id' => $peminjaman->id,
                    'user_id' => $peminjaman->user_id,
                    'barang_id' => $peminjaman->barang_id,
                    'nama_peminjam' => $peminjaman->nama_peminjam,
                    'alasan_meminjam' => $peminjaman->alasan_meminjam,
                    'jumlah' => $peminjaman->jumlah,
                    'tanggal_pinjam' => $peminjaman->tanggal_pinjam,
                    'tanggal_kembali' => $peminjaman->tanggal_kembali,
                    'status' => $peminjaman->status,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan peminjaman.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function index(Request $request) {
        $peminjaman = Peminjaman::with('barang')
            ->orderByDesc('created_at')
            ->get();
    
        return response()->json([
            'success' => true,
            'data' => $peminjaman
        ]);     
    }

    
}