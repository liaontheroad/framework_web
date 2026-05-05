@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Scan QR Code</h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/home">Dashboard</a></li>
            <li class="breadcrumb-item active">Scan QR</li>
        </ul>
    </nav>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Kamera Scanner</h4>
                <p class="card-description">Arahkan kamera ke QR Code milik customer.</p>
                <div id="qr-reader" style="width:100%;"></div>
                <div id="btn-reset-wrapper" style="display:none;" class="mt-3">
                    <button class="btn btn-warning btn-block" onclick="resetScanner()">
                        <i class="mdi mdi-refresh"></i> Scan Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Hasil Scan</h4>
                <p class="card-description">Detail pesanan akan muncul setelah QR berhasil dibaca.</p>

                <div id="result-waiting" class="text-center py-5">
                    <i class="mdi mdi-qrcode-scan" style="font-size:60px; color:#ccc;"></i>
                    <p class="text-muted mt-2">Menunggu scan QR Code...</p>
                </div>

                <div id="result-loading" style="display:none;" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2">Memuat data pesanan...</p>
                </div>

                <div id="result-error" style="display:none;">
                    <div class="alert alert-danger">
                        <i class="mdi mdi-alert-circle"></i>
                        <span id="error-message"></span>
                    </div>
                </div>

                <div id="result-success" style="display:none;">
                    <div class="alert alert-success">
                        <i class="mdi mdi-check-circle"></i> QR Code berhasil dibaca!
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:40%">No. Faktur</th>
                                <td><label class="badge badge-info" id="res-faktur"></label></td>
                            </tr>
                            <tr>
                                <th>Customer</th>
                                <td id="res-customer"></td>
                            </tr>
                            <tr>
                                <th>Status Bayar</th>
                                <td><label class="badge" id="res-status"></label></td>
                            </tr>
                        </table>
                    </div>

                    <h4 class="card-title mt-3">Menu yang Dipesan</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Makanan</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="res-items"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Total</th>
                                    <th id="res-total"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode = null;

    function playBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 880;
            gain.gain.setValueAtTime(1, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.15);
        } catch(e) {}
    }

    function showState(state) {
        ['waiting', 'loading', 'error', 'success'].forEach(s => {
            document.getElementById('result-' + s).style.display = (s === state) ? 'block' : 'none';
        });
    }

    function resetScanner() {
        showState('waiting');
        document.getElementById('btn-reset-wrapper').style.display = 'none';
        startScanner();
    }

    async function onScanSuccess(decodedText) {
        playBeep();

        if (html5QrCode) {
            await html5QrCode.stop();
        }
        document.getElementById('btn-reset-wrapper').style.display = 'block';
        showState('loading');

        try {
            const response = await fetch(`/vendor/pesanan/scan/${encodeURIComponent(decodedText)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Pesanan tidak ditemukan.');
            const data = await response.json();

            document.getElementById('res-faktur').textContent   = data.nomor_faktur;
            document.getElementById('res-customer').textContent = data.nama_customer;

            const statusEl = document.getElementById('res-status');
            statusEl.textContent = data.status_bayar;
            statusEl.className   = 'badge ' + (data.status_bayar === 'Lunas' ? 'badge-success' : 'badge-warning');

            let total = 0;
            const tbody = document.getElementById('res-items');
            tbody.innerHTML = '';
            data.items.forEach(item => {
                total += parseFloat(item.subtotal);
                tbody.innerHTML += `
                    <tr>
                        <td class="font-weight-bold">${item.nama_makanan}</td>
                        <td>${item.jumlah}</td>
                        <td>Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
                    </tr>`;
            });
            document.getElementById('res-total').textContent = 'Rp ' + total.toLocaleString('id-ID');

            showState('success');
        } catch (err) {
            document.getElementById('error-message').textContent = err.message;
            showState('error');
        }
    }

    function startScanner() {
        document.getElementById('qr-reader').innerHTML = '';
        html5QrCode = new Html5Qrcode("qr-reader");
        Html5Qrcode.getCameras().then(cameras => {
            if (cameras && cameras.length) {
                html5QrCode.start(
                    cameras[0].id,
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess
                );
            }
        }).catch(err => {
            document.getElementById('qr-reader').innerHTML = '<p class="text-danger">Kamera tidak dapat diakses: ' + err + '</p>';
        });
    }

    startScanner();
</script>
@endsection