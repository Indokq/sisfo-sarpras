<?php

namespace App\Http\Controllers\Web;

use App\Models\Peminjaman;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with(['barang', 'user'])->get();
        return view('admin.peminjaman.index', compact('peminjaman'));
    }

    public function approve($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $stokBarang = StokBarang::where('id_barang', $peminjaman->barang_id)->first();


        if ($stokBarang && $stokBarang->jumlah >= $peminjaman->jumlah) {
            $peminjaman->status = 'approved';

            // Kurangi stok barang
            $stokBarang->jumlah -= $peminjaman->jumlah;
            $stokBarang->save();

            $peminjaman->save();

            return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil disetujui!');
        } else {
            return redirect()->route('admin.peminjaman.index')->with('error', 'Stok tidak cukup untuk peminjaman!');
        }
    }

    public function reject($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = 'rejected';
        $peminjaman->save();

        return redirect()->route('admin.peminjaman.index')->with('error', 'Peminjaman ditolak!');
    }

}