@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white py-3">
                <h4 class="mb-0 text-white">Wilayah Indonesia (Debug Mode)</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="fw-bold">Provinsi:</label>
                            <select id="provinsi" class="form-control">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="fw-bold">Kota/Kabupaten:</label>
                            <select id="kota" class="form-control">
                                <option value="">Pilih Kota</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="fw-bold">Kecamatan:</label>
                            <select id="kecamatan" class="form-control">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="fw-bold">Kelurahan/Desa:</label>
                            <select id="kelurahan" class="form-control">
                                <option value="">Pilih Kelurahan</option>
                            </select>
                        </div>
                    </div>
                </div> 

                <div class="mt-4 p-3 bg-light rounded border">
                    <small class="text-uppercase font-weight-bold text-muted">Status Debug:</small>
                    <h5 id="debugStatus" class="text-info mt-1">Siap! Silakan pilih provinsi.</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    console.log("DEBUG: Sistem Wilayah Aktif!");

    $('#provinsi').on('change', function() {
        let idProv = $(this).val();
        resetSelect(['kota', 'kecamatan', 'kelurahan']);
        
        if (!idProv) return;
        $('#debugStatus').text("Mengambil data kota...");

        $.ajax({
            url: "{{ route('admin.wilayah.getKota') }}",
            type: "POST",
            data: { id: idProv, _token: "{{ csrf_token() }}" },
            success: function(res) {
                if(res.status === 'success') {
                    let options = '<option value="">Pilih Kota</option>';
                    $.each(res.data, function(key, item) {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#kota').html(options);
                    $('#debugStatus').text("Kota berhasil dimuat.");
                }
            }
        });
    });

    $('#kota').on('change', function() {
        let idKota = $(this).val();
        resetSelect(['kecamatan', 'kelurahan']);
        
        if (!idKota) return;
        $('#debugStatus').text("Mengambil data kecamatan...");

        $.ajax({
            url: "{{ route('admin.wilayah.getKecamatan') }}",
            type: "POST",
            data: { id: idKota, _token: "{{ csrf_token() }}" },
            success: function(res) {
                if(res.status === 'success') {
                    let options = '<option value="">Pilih Kecamatan</option>';
                    $.each(res.data, function(key, item) {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#kecamatan').html(options);
                    $('#debugStatus').text("Kecamatan berhasil dimuat.");
                }
            }
        });
    });

    $('#kecamatan').on('change', function() {
        let idKec = $(this).val();
        resetSelect(['kelurahan']);
        
        if (!idKec) return;
        $('#debugStatus').text("Mengambil data kelurahan...");

        $.ajax({
            url: "{{ route('admin.wilayah.getKelurahan') }}",
            type: "POST",
            data: { id: idKec, _token: "{{ csrf_token() }}" },
            success: function(res) {
                if(res.status === 'success') {
                    let options = '<option value="">Pilih Kelurahan</option>';
                    $.each(res.data, function(key, item) {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#kelurahan').html(options);
                    $('#debugStatus').text("Kelurahan berhasil dimuat.");
                }
            }
        });
    });

    function resetSelect(ids) {
        ids.forEach(id => {
            let label = id.charAt(0).toUpperCase() + id.slice(1);
            $(`#${id}`).html(`<option value="">Pilih ${label}</option>`);
        });
    }
});
</script>
@endsection