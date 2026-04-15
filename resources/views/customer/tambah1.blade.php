@extends('layouts.main')
@section('content')
<div class="page-header">
    <h3 class="page-title">Tambah Customer 1 - Simpan Blob</h3>
</div>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('customer.store1') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Nama Customer</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>No HP</label>
                        <input type="text" name="no_hp" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Foto Customer</label>
                        <div class="text-center">
                            <video id="video" width="100%" height="240" autoplay 
                                   style="border:1px solid #ddd; border-radius:4px;"></video>
                            <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                            <br>
                            <button type="button" class="btn btn-info mt-2" onclick="ambilFoto()">
                                <i class="mdi mdi-camera"></i> Ambil Foto
                            </button>
                        </div>
                        <div class="mt-2 text-center">
                            <img id="preview" src="" width="160" 
                                 style="display:none; border-radius:4px; border:1px solid #ddd;">
                        </div>
                        <input type="hidden" name="foto_data" id="foto_data">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    <a href="{{ route('customer.index') }}" class="btn btn-secondary w-100 mt-2">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            document.getElementById('video').srcObject = stream;
        })
        .catch(err => alert('Kamera tidak bisa diakses: ' + err));

    function ambilFoto() {
        const canvas  = document.getElementById('canvas');
        const video   = document.getElementById('video');
        const preview = document.getElementById('preview');
        canvas.getContext('2d').drawImage(video, 0, 0, 320, 240);
        const dataUrl = canvas.toDataURL('image/jpeg');
        document.getElementById('foto_data').value = dataUrl;
        preview.src = dataUrl;
        preview.style.display = 'block';
    }
</script>
@endpush
@endsection