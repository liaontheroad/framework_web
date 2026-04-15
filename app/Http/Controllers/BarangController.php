<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{

    public function index()
    {
        $barang = DB::table('barang')->orderBy('timestamp', 'desc')->get();
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nama'  => 'required|max:50',
        'harga' => 'required|numeric'
    ]);

    $id = DB::table('barang')->insertGetId([
        'nama'  => $request->nama,
        'harga' => $request->harga
    ]);

    // Generate kode dari tanggal + id
    $kode = 'BRG-' . date('Ymd') . '-' . $id;
    DB::table('barang')->where('id_barang', $id)->update([
        'kode_barang' => $kode
    ]);

    return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
}

    public function edit($id)
    {
        // Ambil data barang berdasarkan id
        $barang = DB::table('barang')->where('id_barang', $id)->first();
        
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|max:50',
            'harga' => 'required|numeric'
        ]);

        // Update data ke database
        DB::table('barang')->where('id_barang', $id)->update([
            'nama' => $request->nama,
            'harga' => $request->harga
        ]);

        return redirect()->route('barang.index')->with('success', 'Data permen berhasil diupdate!');
    }

    public function destroy($id)
    {
        DB::table('barang')->where('id_barang', $id)->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }
    public function cetak(Request $request)
    {
        if(!$request->items) {
            return back()->with('error', 'Pilih minimal 1 permen dulu untuk dicetak!');
        }

        $barang_terpilih = DB::table('barang')->whereIn('id_barang', $request->items)->get();

        $jumlah_kosong = ($request->y - 1) * 5 + ($request->x - 1);

        $dataCetak = [];
        
        for ($i = 0; $i < $jumlah_kosong; $i++) {
            $dataCetak[] = null; 
        }

        foreach ($barang_terpilih as $b) {
            $dataCetak[] = $b;
        }

        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
            foreach ($dataCetak as &$item) {
                if ($item !== null) {
                    $svg = $generator->getBarcode(
                        (string)$item->kode_barang,
                        $generator::TYPE_CODE_128
                    );
                    $item->barcode_base64 = 'data:image/svg+xml;base64,' . base64_encode($svg);
                }
            }
            unset($item);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('barang.pdf', compact('dataCetak'))
                ->setPaper('a4', 'portrait');
        return $pdf->stream('Tag_Harga.pdf');
    }
}
