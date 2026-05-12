<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\LokasiToko;
use App\Models\Kunjungan;

class TokoController extends Controller
{
    public function index()
    {
        $toko    = LokasiToko::orderBy('nama_toko')->get();
        $riwayat = Kunjungan::with('toko', 'user')
                        ->orderBy('waktu_kunjungan', 'desc')
                        ->take(20)
                        ->get();

        return view('toko.index', compact('toko', 'riwayat'));
    }
    public function tambahTokoPage()
    {
        return view('toko.tambah');
    }
    public function kunjungiPage()
    {
        return view('toko.kunjungi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:50',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
        ]);

        do {
            $barcode = strtoupper(Str::random(8));
        } while (LokasiToko::where('barcode', $barcode)->exists());

        LokasiToko::create([
            'barcode'   => $barcode,
            'nama_toko' => $request->nama_toko,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy'  => $request->accuracy,
        ]);

        return redirect()->route('toko.index')->with('success', "Toko berhasil ditambahkan dengan barcode: {$barcode}");
    }

    public function getByBarcode($barcode)
    {
        $toko = LokasiToko::where('barcode', $barcode)->first();

        if (!$toko) {
            return response()->json(['message' => 'Toko tidak ditemukan.'], 404);
        }

        return response()->json($toko);
    }

    public function kunjungi(Request $request)
    {
        $request->validate([
            'barcode'          => 'required|exists:lokasi_toko,barcode',
            'latitude_sales'   => 'required|numeric',
            'longitude_sales'  => 'required|numeric',
            'accuracy_sales'   => 'required|numeric',
        ]);

        $toko = LokasiToko::where('barcode', $request->barcode)->firstOrFail();

        $jarak     = $this->haversine($toko->latitude, $toko->longitude, $request->latitude_sales, $request->longitude_sales);
        $threshold = 300 + $toko->accuracy + $request->accuracy_sales;
        $status    = $jarak <= $threshold ? 'diterima' : 'ditolak';

        $kunjungan = Kunjungan::create([
            'barcode'           => $toko->barcode,
            'user_id'           => Auth::id(),
            'latitude_sales'    => $request->latitude_sales,
            'longitude_sales'   => $request->longitude_sales,
            'accuracy_sales'    => $request->accuracy_sales,
            'jarak_meter'       => round($jarak, 2),
            'threshold_efektif' => round($threshold, 2),
            'status'            => $status,
            'waktu_kunjungan'   => now(),
        ]);

        return response()->json([
            'status'            => $status,
            'jarak_meter'       => round($jarak, 2),
            'threshold_efektif' => round($threshold, 2),
            'toko'              => $toko->nama_toko,
            'barcode'           => $toko->barcode,
            'waktu'             => $kunjungan->waktu_kunjungan,
        ]);
    }

    private function haversine($lat1, $lng1, $lat2, $lng2): float
    {
        $R    = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c    = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }
}