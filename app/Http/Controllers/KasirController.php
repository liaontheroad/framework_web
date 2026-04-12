<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index() {
        return view('modul_ajax.kasir'); 
    }

    // 1. Fungsi Ambil Barang (Sesuai kolom: id, nama, harga)
    public function getBarang($kode) {
        $barang = DB::table('produk')
                    ->where('id', (int)$kode) 
                    ->first();

        if($barang) {
            return response()->json([
                'id'    => $barang->id,   // Ini kodenya (1, 2, atau 3)
                'nama'  => $barang->nama, // Sesuai kolom di SQL baru
                'harga' => $barang->harga
            ]);
        }

        return response()->json(['message' => 'Barang Tidak Ditemukan'], 404);
    }

    // 2. Fungsi Simpan Transaksi (Sesuai ERD: penjualan & penjualan_detail)
    public function store(Request $request) {
        if (!$request->has('items') || count($request->items) == 0) {
            return response()->json(['status' => 'error', 'message' => 'Keranjang kosong!'], 400);
        }

        DB::beginTransaction();
        try {
            // INSERT KE TABEL: penjualan (Header)
            // Kolom sesuai ERD: id_penjualan, timestamp, total
            $penjualanId = DB::table('penjualan')->insertGetId([
                'total'     => $request->total,
                'timestamp' => now()
            ], 'id_penjualan'); // Menyatakan PK-nya adalah id_penjualan

            foreach ($request->items as $item) {
                // INSERT KE TABEL: penjualan_detail
                // Kolom sesuai ERD: id_penjualan, id_produk, jumlah, subtotal
                DB::table('penjualan_detail')->insert([
                    'id_penjualan' => $penjualanId,
                    'id_produk'    => $item['id'], // ID dari tabel produk
                    'jumlah'       => $item['jumlah'],
                    'subtotal'     => $item['subtotal']
                ]);

                // Opsional: Jika ada kolom stok di tabel produk, potong stoknya
                // DB::table('produk')->where('id', $item['id'])->decrement('stok', $item['jumlah']);
            }

            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran transaksi berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }
}