@extends('layouts.main')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-purple text-white">
            <h3 class="card-title mb-0">Daftar Tag Harga Barang</h3>
        </div>
        <div class="card-body">
            
            <a href="{{ route('barang.create') }}" class="btn btn-primary mb-4">
                + Tambah Barang Baru
            </a>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('barang.cetak') }}" method="POST" target="_blank" id="form-cetak">
                @csrf
                <div class="row mb-4 p-3 bg-light rounded border">
                    <div class="col-md-12 mb-2">
                        <strong>Pengaturan Cetak Label (Tom n Jerry 108):</strong>
                    </div>
                    <div class="col-md-3">
                        <label>Mulai dari Kolom (X) :</label>
                        <input type="number" name="x" class="form-control" value="1" min="1" max="5" required>
                    </div>
                    <div class="col-md-3">
                        <label>Mulai dari Baris (Y) :</label>
                        <input type="number" name="y" class="form-control" value="1" min="1" max="8" required>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="mdi mdi-printer"></i> Cetak Tag Barang
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabel-barang" class="table table-striped table-bordered text-center">
                        <thead>
                            <tr>
                                <th width="5%"><input type="checkbox" id="checkAll"></th>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barang as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" name="items[]" value="{{ $item->id_barang }}" class="checkItem">
                                </td>
                                <td><strong>{{ $item->id_barang }}</strong></td>
                                <td>{{ $item->nama }}</td>
                                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('barang.destroy', $item->id_barang) }}" 
                                       class="btn btn-danger btn-sm"
                                       onclick="event.preventDefault(); if(confirm('Yakin mau menghapus?')) document.getElementById('delete-form-{{ $item->id_barang }}').submit();">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            @foreach($barang as $item)
            <form id="delete-form-{{ $item->id_barang }}" action="{{ route('barang.destroy', $item->id_barang) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            @endforeach

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        
        var table = $('#tabel-barang').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }, 
            "ordering": false
        });
        
        $(document).on('change', '#checkAll', function() {
            var statusCentang = $(this).prop('checked');
            
            $('.checkItem').prop('checked', statusCentang);
            
            table.$('.checkItem').prop('checked', statusCentang);
        });

        $('#form-cetak').on('submit', function(e) {
            var form = this;
            table.$('.checkItem:checked').each(function(){
                if(!$.contains(document, this)){
                    $(form).append(
                        $('<input>').attr('type', 'hidden').attr('name', this.name).val(this.value)
                    );
                }
            });
        });
    });
</script>
@endsection