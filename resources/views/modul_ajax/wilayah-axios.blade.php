@extends('layouts.main')

@section('title-page', 'Manajemen Wilayah - Axios Version')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0 text-white">Wilayah Administrasi Indonesia (Axios Library)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="fw-bold text-dark">Provinsi</label>
                            <select id="provinsi" class="form-control form-select">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="fw-bold text-dark">Kota/Kabupaten</label>
                            <select id="kota" class="form-control form-select">
                                <option value="">Pilih Kota</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="fw-bold text-dark">Kecamatan</label>
                            <select id="kecamatan" class="form-control form-select">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="fw-bold text-dark">Kelurahan/Desa</label>
                            <select id="kelurahan" class="form-control form-select">
                                <option value="">Pilih Kelurahan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded border">
                    <div class="d-flex align-items-center">
                        <div id="loader" class="spinner-border spinner-border-sm text-primary me-2 d-none" role="status"></div>
                        <span id="statusInfo" class="text-muted small text-uppercase fw-bold">Silakan pilih provinsi untuk memulai</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    const resetDropdowns = (ids) => {
        ids.forEach(id => {
            const label = id.charAt(0).toUpperCase() + id.slice(1);
            document.getElementById(id).innerHTML = `<option value="">Pilih ${label}</option>`;
        });
    };
    const toggleLoader = (show, message = "") => {
        const loader = document.getElementById('loader');
        const statusText = document.getElementById('statusInfo');
        
        if(show) {
            loader.classList.remove('d-none');
            statusText.innerText = message;
            statusText.classList.replace('text-muted', 'text-primary');
        } else {
            loader.classList.add('d-none');
            statusText.innerText = message || "Selesai";
            statusText.classList.replace('text-primary', 'text-success');
        }
    };

    document.getElementById('provinsi').addEventListener('change', function() {
        const idProv = this.value;
        resetDropdowns(['kota', 'kecamatan', 'kelurahan']);

        if (!idProv) return;

        toggleLoader(true, "Mengambil data kota...");

        axios.post("{{ route('api.getKota') }}", { 
            id: idProv, 
            _token: "{{ csrf_token() }}" 
        })
        .then(res => {
            let options = '<option value="">Pilih Kota</option>';
            res.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            document.getElementById('kota').innerHTML = options;
            toggleLoader(false, "Data kota berhasil dimuat");
        })
        .catch(err => {
            console.error(err);
            toggleLoader(false, "Terjadi kesalahan!");
        });
    });

    document.getElementById('kota').addEventListener('change', function() {
        const idKota = this.value;
        resetDropdowns(['kecamatan', 'kelurahan']);

        if (!idKota) return;

        toggleLoader(true, "Mengambil data kecamatan...");

        axios.post("{{ route('api.getKecamatan') }}", { 
            id: idKota, 
            _token: "{{ csrf_token() }}" 
        })
        .then(res => {
            let options = '<option value="">Pilih Kecamatan</option>';
            res.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            document.getElementById('kecamatan').innerHTML = options;
            toggleLoader(false, "Data kecamatan berhasil dimuat");
        });
    });
    
    document.getElementById('kecamatan').addEventListener('change', function() {
        const idKec = this.value;
        resetDropdowns(['kelurahan']);

        if (!idKec) return;

        toggleLoader(true, "Mengambil data kelurahan...");

        axios.post("{{ route('api.getKelurahan') }}", { 
            id: idKec, 
            _token: "{{ csrf_token() }}" 
        })
        .then(res => {
            let options = '<option value="">Pilih Kelurahan</option>';
            res.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            document.getElementById('kelurahan').innerHTML = options;
            toggleLoader(false, "Data kelurahan berhasil dimuat");
        });
    });

</script>
@endsection