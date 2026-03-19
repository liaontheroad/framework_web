<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- TAMBAHKAN INI BIAR GAK ERROR LAGI
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

class WilayahController extends Controller
{
    public function wilayah()
    {
        $provinces = Province::orderBy('name', 'asc')->get();
        return view('modul_ajax.wilayah', compact('provinces'));
    }

    public function index()
    {
        $provinces = DB::table('reg_provinces')->orderBy('name', 'asc')->get();
        return view('modul_ajax.wilayah-axios', compact('provinces'));
    }

    public function getKota(Request $request) 
    {
        $id = $request->id;
        $kota = DB::table('reg_regencies')
                    ->where('province_id', $id)
                    ->orderBy('name', 'asc')
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $kota
        ]);
    }

    public function getKecamatan(Request $request)
    {
        $id_kota = $request->id;
        $kecamatan = District::where('regency_id', $id_kota)
                             ->orderBy('name', 'asc')
                             ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $kecamatan
        ]);
    }

    public function getKelurahan(Request $request)
    {
        $id_kec = $request->id;
        $kelurahan = Village::where('district_id', $id_kec)
                            ->orderBy('name', 'asc')
                            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $kelurahan
        ]);
    }

    public function axiosGetKota(Request $request) 
    {
        $data = DB::table('reg_regencies')
                    ->where('province_id', $request->id)
                    ->orderBy('name', 'asc')
                    ->get();
        return response()->json($data);
    }

    public function axiosGetKecamatan(Request $request) 
    {
        $data = DB::table('reg_districts')
                    ->where('regency_id', $request->id)
                    ->orderBy('name', 'asc')
                    ->get();
        return response()->json($data);
    }

    public function axiosGetKelurahan(Request $request) 
    {
        $data = DB::table('reg_villages')
                    ->where('district_id', $request->id)
                    ->orderBy('name', 'asc')
                    ->get();
        return response()->json($data);
    }
}