@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Kunjungan Toko</h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/home">Dashboard</a></li>
            <li class="breadcrumb-item active">Kunjungan Toko</li>
        </ul>
    </nav>
</div>

{{-- ── LIST TOKO ───────────────────────────────────────────── --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-0">List Toko</h4>
                        <p class="card-description mb-0">Daftar toko beserta barcode dan koordinat titik awal.</p>
                    </div>
                    <a href="{{ route('toko.tambah') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle"></i> Tambah Toko
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Toko</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Accuracy (m)</th>
                                <th class="text-center">Cetak QR Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($toko as $t)
                            <tr>
                                <td><label class="badge badge-dark">{{ $t->barcode }}</label></td>
                                <td class="font-weight-bold">{{ $t->nama_toko }}</td>
                                <td><label class="badge badge-info">{{ $t->latitude }}</label></td>
                                <td><label class="badge badge-info">{{ $t->longitude }}</label></td>
                                <td><label class="badge badge-{{ $t->accuracy <= 50 ? 'success' : 'warning' }}">{{ $t->accuracy }} m</label></td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm"
                                        onclick="cetakQR('{{ $t->barcode }}', '{{ $t->nama_toko }}')">
                                        <i class="mdi mdi-qrcode"></i> Cetak QR Code
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">Belum ada data toko.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── RIWAYAT KUNJUNGAN ───────────────────────────────────── --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-0">Riwayat Kunjungan</h4>
                        <p class="card-description mb-0">20 kunjungan terakhir.</p>
                    </div>
                    <a href="{{ route('toko.kunjungi.page') }}" class="btn btn-success">
                        <i class="mdi mdi-map-marker-check"></i> Input Kunjungan
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Sales</th>
                                <th>Barcode</th>
                                <th>Toko</th>
                                <th>Jarak</th>
                                <th>Threshold</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $r)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($r->waktu_kunjungan)->format('d/m/Y H:i') }}</td>
                                <td>{{ $r->user->name ?? '-' }}</td>
                                <td><label class="badge badge-dark">{{ $r->barcode }}</label></td>
                                <td class="font-weight-bold">{{ $r->toko->nama_toko ?? '-' }}</td>
                                <td>{{ $r->jarak_meter }} m</td>
                                <td>{{ $r->threshold_efektif }} m</td>
                                <td>
                                    <label class="badge badge-{{ $r->status === 'diterima' ? 'success' : 'danger' }}">
                                        {{ strtoupper($r->status) }}
                                    </label>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted">Belum ada riwayat kunjungan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL QR CODE ───────────────────────────────────────── --}}
<div class="modal fade" id="modalQR" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-qrcode"></i> QR Code Toko</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <p class="font-weight-bold mb-1" id="modal_nama_toko"></p>
                <p class="text-muted small mb-3" id="modal_barcode_text"></p>
                <div id="qr_canvas" class="d-flex justify-content-center"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-block" onclick="printQR()">
                    <i class="mdi mdi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function cetakQR(barcode, nama) {
        document.getElementById('modal_nama_toko').textContent  = nama;
        document.getElementById('modal_barcode_text').textContent = 'Barcode: ' + barcode;
        document.getElementById('qr_canvas').innerHTML = '';
        new QRCode(document.getElementById('qr_canvas'), {
            text:         barcode,
            width:        200,
            height:       200,
            colorDark:    '#000000',
            colorLight:   '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });
        $('#modalQR').modal('show');
    }

    function printQR() {
        const nama    = document.getElementById('modal_nama_toko').textContent;
        const barcode = document.getElementById('modal_barcode_text').textContent;
        const imgEl   = document.querySelector('#qr_canvas img');
        const src     = imgEl ? imgEl.src : '';
        const win     = window.open('', '_blank');
        win.document.write(`<html><body style="text-align:center;font-family:sans-serif;padding:40px;">
            <h2>${nama}</h2>
            <img src="${src}" style="width:220px;height:220px;">
            <p style="font-size:18px;letter-spacing:4px;margin-top:10px;">${barcode}</p>
        </body></html>`);
        win.document.close();
        win.print();
    }
</script>
@endsection