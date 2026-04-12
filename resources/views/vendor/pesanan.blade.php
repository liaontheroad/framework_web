@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Pesanan Masuk</h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/home">Dashboard</a></li>
            <li class="breadcrumb-item active">Pesanan</li>
        </ul>
    </nav>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pesanan Lunas</h4>
                <p class="card-description">Daftar pesanan yang sudah dibayar oleh customer.</p>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No. Faktur</th>
                                <th>Customer</th>
                                <th>Menu</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan as $p)
                            <tr>
                                <td><label class="badge badge-info">{{ $p->nomor_faktur }}</label></td>
                                <td>{{ $p->nama_customer }}</td>
                                <td class="font-weight-bold">{{ $p->nama_makanan }}</td>
                                <td>{{ $p->jumlah }}</td>
                                <td>Rp {{ number_format($p->subtotal, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tanggal_transaksi)->format('d/m/Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center">Belum ada pesanan lunas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection