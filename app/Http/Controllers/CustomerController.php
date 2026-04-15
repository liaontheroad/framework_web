<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    // Data Customer
    public function index()
    {
        $customers = DB::table('customers')->orderBy('id', 'desc')->get();
        return view('customer.index', compact('customers'));
    }

    // Form Tambah Customer 1 (blob)
    public function tambah1()
    {
        return view('customer.tambah1');
    }

    // Simpan Customer 1 (blob ke database)
   public function store1(Request $request)
    {
        $request->validate(['nama' => 'required']);

        $foto_hex = null;
        if ($request->foto_data) {
            $image_data = explode(',', $request->foto_data)[1];
            $foto_hex   = bin2hex(base64_decode($image_data));
        }

        DB::statement("
            INSERT INTO customers (nama, email, no_hp, alamat, foto_blob, created_at, updated_at)
            VALUES (?, ?, ?, ?, decode(?, 'hex'), ?, ?)
        ", [
            $request->nama,
            $request->email,
            $request->no_hp,
            $request->alamat,
            $foto_hex,
            now(),
            now()
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer 1 berhasil ditambahkan!');
    }

    // Form Tambah Customer 2 (file)
    public function tambah2()
    {
        return view('customer.tambah2');
    }

    // Simpan Customer 2 (file ke storage)
    public function store2(Request $request)
    {
        $request->validate(['nama' => 'required']);

        $foto_path = null;
        if ($request->foto_data) {
            $image_data = explode(',', $request->foto_data)[1];
            $filename   = 'customer_' . time() . '.jpg';
            Storage::disk('public')->put('customers/' . $filename, base64_decode($image_data));
            $foto_path  = 'customers/' . $filename;
        }

        DB::table('customers')->insert([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'no_hp'      => $request->no_hp,
            'alamat'     => $request->alamat,
            'foto_path' => $foto_path, 
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer 2 berhasil ditambahkan!');
    }
}