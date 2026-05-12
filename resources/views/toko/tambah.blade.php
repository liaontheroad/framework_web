@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Tambah Toko</h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/home">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('toko.index') }}">Kunjungan Toko</a></li>
            <li class="breadcrumb-item active">Tambah Toko</li>
        </ul>
    </nav>
</div>

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Input Titik Awal</h4>
                <p class="card-description">Tambah toko baru dan simpan koordinat lokasi awal toko. Barcode akan di-generate otomatis oleh sistem.</p>

                <form action="{{ route('toko.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Nama Toko</label>
                        <input type="text" name="nama_toko" class="form-control" required maxlength="50"
                            placeholder="Contoh: Toko Budi Jaya">
                    </div>
                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="text" name="latitude" id="lat_toko" class="form-control bg-light" readonly required>
                    </div>
                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="lng_toko" class="form-control bg-light" readonly required>
                    </div>
                    <div class="form-group">
                        <label>Accuracy (meter)</label>
                        <input type="text" name="accuracy" id="acc_toko" class="form-control bg-light" readonly required>
                    </div>

                    <button type="button" class="btn btn-info btn-block mb-2" onclick="ambilLokasiToko()">
                        <i class="mdi mdi-crosshairs-gps"></i> Ambil Lokasi Toko
                    </button>
                    <div id="status_toko" class="text-center small mb-3"></div>

                    <button type="submit" class="btn btn-primary btn-block" id="btn_simpan_toko" disabled>
                        <i class="mdi mdi-content-save"></i> Simpan Toko
                    </button>
                    <a href="{{ route('toko.index') }}" class="btn btn-secondary btn-block mt-2">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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

    async function ambilLokasiToko() {
        const statusEl = document.getElementById('status_toko');
        statusEl.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Mengambil lokasi, mohon tunggu...';
        document.getElementById('btn_simpan_toko').disabled = true;
        try {
            const pos = await getAccuratePosition(50, 20000);
            document.getElementById('lat_toko').value = pos.coords.latitude;
            document.getElementById('lng_toko').value = pos.coords.longitude;
            document.getElementById('acc_toko').value = pos.coords.accuracy.toFixed(2);
            statusEl.innerHTML = `<span class="text-success"><i class="mdi mdi-check-circle"></i> Lokasi didapat! Accuracy: ${pos.coords.accuracy.toFixed(2)} m</span>`;
            document.getElementById('btn_simpan_toko').disabled = false;
        } catch(err) {
            statusEl.innerHTML = `<span class="text-danger"><i class="mdi mdi-alert-circle"></i> Gagal: ${err.message}</span>`;
        }
    }
</script>
@endsection