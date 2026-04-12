@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Kelola Menu</h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/home">Dashboard</a></li>
            <li class="breadcrumb-item active">Menu</li>
        </ul>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Menu</h4>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form action="/vendor/menu/store" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Nama Makanan</label>
                        <input type="text" name="nama_makanan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Menu - {{ $vendor->nama_vendor }}</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Makanan</th>
                                <th>Harga</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($makanan as $m)
                            <tr>
                                <td><label class="badge badge-info">{{ $m->kode_makanan }}</label></td>
                                <td class="font-weight-bold">{{ $m->nama_makanan }}</td>
                                <td>Rp {{ number_format($m->harga, 0, ',', '.') }}</td>
                                <td><label class="badge badge-{{ $m->stok > 0 ? 'success' : 'danger' }}">{{ $m->stok }}</label></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center">Belum ada menu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection