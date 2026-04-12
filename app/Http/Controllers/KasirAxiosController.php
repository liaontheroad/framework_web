<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirAxiosController extends Controller
{
    public function index() {
        return view('modul_ajax.kasir');
    }

    // Mengambil data dari tabel 'barang' (Modul Lama)
    public function getBarangAxios($kode) {
        $barang = DB::table('barang')
                    ->where('id_barang', $kode) 
                    ->first();

        if($barang) {
            return response()->json($barang);
        }

        return response()->json(['message' => 'Barang Tidak Ditemukan!'], 404);
    }

    public function storeAxios(Request $request) {
        if (!$request->items || count($request->items) == 0) {
            return response()->json(['message' => 'Keranjang kosong!'], 400);
        }

        DB::beginTransaction();
        try {
            // Simpan ke tabel 'penjualan' (Modul Lama)
            $penjualanId = DB::table('penjualan')->insertGetId([
                'nomor_faktur' => 'TRX-' . strtoupper(uniqid()),
                'total_harga' => $request->total,
                'tgl_jual' => now()
            ]);

            foreach ($request->items as $item) {
                // Simpan ke 'detail_penjualan'
                DB::table('detail_penjualan')->insert([
                    'penjualan_id' => $penjualanId,
                    'id_barang'    => $item['id_barang'], // Primary Key Modul Lama
                    'qty'          => $item['jumlah'],
                    'subtotal'     => $item['subtotal']
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Transaksi Berhasil!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}