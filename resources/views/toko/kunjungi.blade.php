@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Input Kunjungan</h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/home">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('toko.index') }}">Kunjungan Toko</a></li>
            <li class="breadcrumb-item active">Input Kunjungan</li>
        </ul>
    </nav>
</div>

<div class="row">
    {{-- Scanner --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Scan QR Code Toko</h4>
                <p class="card-description">Arahkan kamera ke QR Code yang ada di toko.</p>

                <div id="qr-reader" style="width:100%; min-height:450px;"></div>
                <div id="status_scan" class="text-center small mt-2"></div>

                {{-- Info toko setelah scan --}}
                <div id="info_toko" style="display:none;" class="mt-3">
                    <div class="alert alert-info py-2 mb-2">
                        <strong><i class="mdi mdi-store"></i> <span id="scan_nama_toko"></span></strong><br>
                        <small>
                            Barcode: <span id="scan_barcode"></span><br>
                            Lat: <span id="scan_lat"></span> |
                            Lng: <span id="scan_lng"></span> |
                            Acc: <span id="scan_acc"></span> m
                        </small>
                    </div>
                    <button class="btn btn-outline-secondary btn-sm btn-block" onclick="resetScanner()">
                        <i class="mdi mdi-refresh"></i> Scan Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Lokasi Sales + Hasil --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Lokasi Sales</h4>
                <p class="card-description">Ambil lokasi posisi sales saat ini.</p>

                <div class="form-group">
                    <label>Latitude Sales</label>
                    <input type="text" id="lat_sales" class="form-control bg-light" readonly>
                </div>
                <div class="form-group">
                    <label>Longitude Sales</label>
                    <input type="text" id="lng_sales" class="form-control bg-light" readonly>
                </div>
                <div class="form-group">
                    <label>Accuracy Sales (meter)</label>
                    <input type="text" id="acc_sales" class="form-control bg-light" readonly>
                </div>

                <button type="button" class="btn btn-info btn-block mb-2" onclick="ambilLokasiSales()">
                    <i class="mdi mdi-crosshairs-gps"></i> Ambil Lokasi Saya
                </button>
                <div id="status_sales" class="text-center small mb-3"></div>

                <button type="button" class="btn btn-success btn-block" id="btn_kunjungi"
                    onclick="prosesKunjungan()" disabled>
                    <i class="mdi mdi-map-marker-check"></i> Proses Kunjungan
                </button>

                <a href="{{ route('toko.index') }}" class="btn btn-secondary btn-block mt-2">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>

                {{-- Hasil Kunjungan --}}
                <div id="hasil_kunjungan" class="mt-3" style="display:none;">
                    <div id="hasil_alert" class="alert">
                        <h5 id="hasil_status" class="font-weight-bold mb-2"></h5>
                        <table class="table table-sm table-bordered mb-0 bg-white">
                            <tr><th width="45%">Toko</th><td id="hasil_toko"></td></tr>
                            <tr><th>Barcode</th><td id="hasil_barcode"></td></tr>
                            <tr><th>Jarak Aktual</th><td id="hasil_jarak"></td></tr>
                            <tr><th>Threshold Efektif</th><td id="hasil_threshold"></td></tr>
                            <tr><th>Waktu</th><td id="hasil_waktu"></td></tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    #qr-reader { border: 2px solid #e4e4e4 !important; border-radius: 8px !important; overflow: hidden !important; background: #000 !important; }
    #qr-reader > div:first-child { border: none !important; padding: 0 !important; }
    #qr-reader__dashboard_section_swaplink,
    #qr-reader__status_span,
    #qr-reader__header_message { display: none !important; }
    #qr-reader__camera_selection { width: 100% !important; margin: 8px 0 !important; padding: 6px !important; border-radius: 4px !important; border: 1px solid #ced4da !important; }
    #qr-reader__dashboard_section_csr button,
    #qr-reader__dashboard_section_fsr button { background: #4B49AC !important; color: white !important; border: none !important; padding: 8px 20px !important; border-radius: 4px !important; cursor: pointer !important; width: 100% !important; margin: 8px 0 !important; }
    #qr-reader video { width: 100% !important; border-radius: 0 !important; }
</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode    = null;
    let tokoSiap       = false;
    let lokasiSiap     = false;
    let scannedBarcode = null;

    function startScanner() {
        document.getElementById('qr-reader').innerHTML = '';
        html5QrCode = new Html5Qrcode('qr-reader');
        Html5Qrcode.getCameras().then(cameras => {
            if (cameras && cameras.length) {
                html5QrCode.start(
                    cameras[0].id,
                    { fps: 10, qrbox: { width: 400, height: 400 } },
                    onScanSuccess
                );
            }
        }).catch(() => {
            document.getElementById('status_scan').innerHTML =
                '<span class="text-danger">Kamera tidak dapat diakses.</span>';
        });
    }

    async function onScanSuccess(decodedText) {
        try { await html5QrCode.stop(); } catch(e) {}

        document.getElementById('status_scan').innerHTML =
            '<span class="text-info"><i class="mdi mdi-loading mdi-spin"></i> Mengambil data toko...</span>';

        try {
            const res = await fetch(`/toko/barcode/${encodeURIComponent(decodedText)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('Barcode tidak ditemukan.');
            const data = await res.json();

            scannedBarcode = data.barcode;
            document.getElementById('scan_nama_toko').textContent = data.nama_toko;
            document.getElementById('scan_barcode').textContent   = data.barcode;
            document.getElementById('scan_lat').textContent       = data.latitude;
            document.getElementById('scan_lng').textContent       = data.longitude;
            document.getElementById('scan_acc').textContent       = data.accuracy;
            document.getElementById('info_toko').style.display    = 'block';
            document.getElementById('qr-reader').style.display    = 'none';
            document.getElementById('status_scan').innerHTML      = '';

            tokoSiap = true;
            cekSiapKunjungi();
        } catch(err) {
            document.getElementById('status_scan').innerHTML =
                `<span class="text-danger">${err.message}</span>`;
            startScanner();
        }
    }

    function resetScanner() {
        scannedBarcode = null;
        tokoSiap       = false;
        lokasiSiap     = false;
        document.getElementById('info_toko').style.display       = 'none';
        document.getElementById('qr-reader').style.display       = 'block';
        document.getElementById('hasil_kunjungan').style.display  = 'none';
        document.getElementById('btn_kunjungi').disabled          = true;
        startScanner();
    }

    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
        return new Promise((resolve, reject) => {
            let bestResult = null;
            const startTime = Date.now();
            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const acc = position.coords.accuracy;
                    if (!bestResult || acc < bestResult.coords.accuracy) bestResult = position;
                    if (acc <= targetAccuracy) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                    }
                    if (Date.now() - startTime >= maxWait) {
                        navigator.geolocation.clearWatch(watchId);
                        if (bestResult) resolve(bestResult);
                        else reject(new Error('Timeout, tidak dapat posisi'));
                    }
                },
                (error) => reject(error),
                { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
            );
        });
    }

    async function ambilLokasiSales() {
        const statusEl = document.getElementById('status_sales');
        statusEl.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Mengambil lokasi...';
        lokasiSiap = false;
        try {
            const pos = await getAccuratePosition(50, 20000);
            document.getElementById('lat_sales').value = pos.coords.latitude;
            document.getElementById('lng_sales').value = pos.coords.longitude;
            document.getElementById('acc_sales').value = pos.coords.accuracy.toFixed(2);
            statusEl.innerHTML = `<span class="text-success"><i class="mdi mdi-check-circle"></i> Lokasi didapat! Accuracy: ${pos.coords.accuracy.toFixed(2)} m</span>`;
            lokasiSiap = true;
            cekSiapKunjungi();
        } catch(err) {
            statusEl.innerHTML = `<span class="text-danger"><i class="mdi mdi-alert-circle"></i> Gagal: ${err.message}</span>`;
        }
    }

    function cekSiapKunjungi() {
        document.getElementById('btn_kunjungi').disabled = !(tokoSiap && lokasiSiap);
    }

    async function prosesKunjungan() {
        const btn = document.getElementById('btn_kunjungi');
        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Memproses...';

        try {
            const res = await fetch('{{ route("toko.kunjungi") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    barcode:         scannedBarcode,
                    latitude_sales:  document.getElementById('lat_sales').value,
                    longitude_sales: document.getElementById('lng_sales').value,
                    accuracy_sales:  document.getElementById('acc_sales').value,
                })
            });
            const data = await res.json();
            tampilkanHasil(data);
        } catch(err) {
            alert('Terjadi kesalahan: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-map-marker-check"></i> Proses Kunjungan';
        }
    }

    function tampilkanHasil(data) {
        const diterima = data.status === 'diterima';
        document.getElementById('hasil_alert').className        = 'alert alert-' + (diterima ? 'success' : 'danger');
        document.getElementById('hasil_status').textContent     = diterima ? '✅ KUNJUNGAN DITERIMA' : '❌ KUNJUNGAN DITOLAK';
        document.getElementById('hasil_toko').textContent       = data.toko;
        document.getElementById('hasil_barcode').textContent    = data.barcode;
        document.getElementById('hasil_jarak').textContent      = data.jarak_meter + ' m';
        document.getElementById('hasil_threshold').textContent  = data.threshold_efektif + ' m';
        document.getElementById('hasil_waktu').textContent      = data.waktu;
        document.getElementById('hasil_kunjungan').style.display = 'block';
    }

    startScanner();
</script>
@endsection