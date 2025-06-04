<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\Barang;

class PengembalianController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'peminjaman_id' => 'required|exists:peminjamans,id',
            'tanggal_pengembalian' => 'required|date',
            'jumlah_dikembalikan' => 'required|integer|min:1',
            'status_pengembalian' => 'required|in:pending,completed,damaged',
            'keterangan' => 'nullable|string',
            'denda' => 'nullable|numeric|min:0',
        ]);

        $peminjaman = Peminjaman::with('barang.stok')->findOrFail($validated['peminjaman_id']);

        if ($peminjaman->status === 'returned') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman ini sudah ditandai sebagai dikembalikan.'
            ], 400);
        }

        $pengembalian = Pengembalian::create($validated);

        // Update status peminjaman ke 'returned'
        $peminjaman->update(['status' => 'returned']);

        // Tambah stok barang kembali
        if ($peminjaman->barang && $peminjaman->barang->stok) {
            $peminjaman->barang->stok->increment('jumlah', $validated['jumlah_dikembalikan']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengembalian berhasil disimpan.',
            'data' => $pengembalian
        ], 201);
    }
}
