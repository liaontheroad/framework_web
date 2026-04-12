<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KantinController extends Controller
{
    public function index() {
        $vendors = DB::table('vendors')->get();
        return view('modul_ajax.cashier', compact('vendors'));
    }

    public function getMenu($vendor_id) {
        $menu = DB::table('makanan')->where('vendor_id', $vendor_id)->get();
        return response()->json($menu);
    }

    public function manageMenu() {
    $vendor = DB::table('vendors')
        ->where('user_id', auth()->id())
        ->first();
    
    $makanan = DB::table('makanan')
        ->where('vendor_id', $vendor->id)
        ->get();
    
    return view('vendor.menu', compact('makanan', 'vendor'));
}

public function storeMenu(Request $request) {
    $vendor = DB::table('vendors')
        ->where('user_id', auth()->id())
        ->first();

    DB::table('makanan')->insert([
        'vendor_id'    => $vendor->id,
        'kode_makanan' => 'MKN-' . time(),
        'nama_makanan' => $request->nama_makanan,
        'harga'        => $request->harga,
        'stok'         => $request->stok,
    ]);

    return redirect()->route('vendor.menu')->with('success', 'Menu berhasil ditambahkan!');
}

public function pesananMasuk() {
    $vendor = DB::table('vendors')
        ->where('user_id', auth()->id())
        ->first();

    $pesanan = DB::table('pembelian')
        ->join('detail_pembelian', 'pembelian.id', '=', 'detail_pembelian.pembelian_id')
        ->join('makanan', 'detail_pembelian.makanan_id', '=', 'makanan.id')
        ->where('makanan.vendor_id', $vendor->id)
        ->where('pembelian.status_bayar', 'Lunas')
        ->select(
            'pembelian.id',
            'pembelian.nomor_faktur',
            'pembelian.nama_customer',
            'pembelian.total_harga',
            'pembelian.tanggal_transaksi',
            'makanan.nama_makanan',
            'detail_pembelian.jumlah',
            'detail_pembelian.subtotal'
        )
        ->get();

    return view('vendor.pesanan', compact('pesanan'));
}
public function simpanPesanan(Request $request) {
    DB::beginTransaction();
    try {
        $count = DB::table('pembelian')->count() + 1;
        $guestName = 'Guest_' . str_pad($count, 7, '0', STR_PAD_LEFT);
        $orderId = 'INV-' . time();

        $pembelianId = DB::table('pembelian')->insertGetId([
            'nomor_faktur'      => $orderId,
            'nama_customer'     => $guestName,
            'total_harga'       => $request->total,
            'status_bayar'      => 'Belum Lunas',
            'tanggal_transaksi' => now()
        ]);

        $items = json_decode(json_encode($request->items), true);
        foreach ($items as $item) {
            DB::table('detail_pembelian')->insert([
                'pembelian_id' => $pembelianId,
                'makanan_id'   => $item['makanan_id'],
                'jumlah'       => $item['qty'],
                'harga_satuan' => $item['harga'],
                'subtotal'     => $item['subtotal']
            ]);
            DB::table('makanan')->where('id', $item['makanan_id'])->decrement('stok', $item['qty']);
        }

        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized  = false;
        \Midtrans\Config::$is3ds        = false;
        \Midtrans\Config::$curlOptions  = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $request->total,
            ],
            'customer_details' => [
                'first_name' => $guestName,
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        DB::table('pembelian')->where('id', $pembelianId)->update(['snap_token' => $snapToken]);

        DB::commit();
        return response()->json([
            'snap_token' => $snapToken,
            'order_id'   => $pembelianId
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Gagal simpan: ' . $e->getMessage()], 500);
    }
}

    public function konfirmasiBayar($id) {
    // Cari pembelian berdasarkan ID
    $pembelian = DB::table('pembelian')->where('id', $id)->first();
    
    if ($pembelian) {
        DB::table('pembelian')->where('id', $id)->update([
            'status_bayar' => 'Lunas'
        ]);
        return response()->json(['status' => 'success']);
    }
    
    return response()->json(['status' => 'error'], 404);
}
}