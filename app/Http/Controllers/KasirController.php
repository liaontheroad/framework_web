<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirAxiosController extends Controller
{
    public function index() {
        return view('modul_ajax.kasir');
    }

    public function getBarangAxios($kode) {
        $barang = DB::table('produk')
                    ->where('kode_produk', $kode) 
                    ->first();

        if($barang) {
            return response()->json($barang);
        }

        return response()->json(['message' => 'Barang Tidak Ditemukan di Database'], 404);
    }

    public function storeAxios(Request $request) {

        if (!$request->has('items') || count($request->items) == 0) {
            return response()->json(['message' => 'Keranjang masih kosong!'], 400);
        }

        DB::beginTransaction();
        try {
            
            $penjualanId = DB::table('penjualan')->insertGetId([
                'nomor_faktur' => 'TRX-' . strtoupper(uniqid()),
                'user_id' => 1, 
                'total_harga' => $request->total,
                'bayar' => $request->total,
                'kembali' => 0,
                'tanggal_transaksi' => now()
            ]);

            foreach ($request->items as $item) {

                $produk = DB::table('produk')->where('id', $item['id'])->first();

                if (!$produk) {
                    throw new \Exception("Produk ID {$item['id']} tidak ditemukan!");
                }

                if ($produk->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$produk->nama_produk} tidak mencukupi!");
                }
                DB::table('detail_penjualan')->insert([
                    'penjualan_id' => $penjualanId,
                    'produk_id'    => $item['id'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $item['harga'],
                    'subtotal'     => $item['subtotal']
                ]);
                DB::table('produk')
                    ->where('id', $item['id'])
                    ->decrement('stok', $item['jumlah']);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi Berhasil Disimpan!',
                'faktur' => 'TRX-' . time()
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