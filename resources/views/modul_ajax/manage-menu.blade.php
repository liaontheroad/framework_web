@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card shadow border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title text-primary"><i class="mdi mdi-food"></i> Vendor Menu Management</h4>
                    <button class="btn btn-gradient-primary" onclick="openModal()">
                        <i class="mdi mdi-plus"></i> Add New Menu
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $m)
                            <tr>
                                <td>{{ $m->kode_barang }}</td>
                                <td>{{ $m->nama_barang }}</td>
                                <td>Rp {{ number_format($m->harga) }}</td>
                                <td>{{ $m->stok }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick='editData(@json($m))'>Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData({{ $m->id }})">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center">No menu available.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMenu" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Form Menu</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formMenu">
                <div class="modal-body">
                    <input type="hidden" id="m_id">
                    <div class="form-group">
                        <label>Item Code</label>
                        <input type="text" class="form-control" id="m_kode" required>
                    </div>
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" class="form-control" id="m_nama" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" id="m_harga" required>
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" class="form-control" id="m_stok" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function openModal() {
        $('#formMenu')[0].reset();
        $('#m_id').val('');
        $('#modalMenu').modal('show');
    }

    function editData(data) {
        $('#m_id').val(data.id);
        $('#m_kode').val(data.kode_barang);
        $('#m_nama').val(data.nama_barang);
        $('#m_harga').val(data.harga);
        $('#m_stok').val(data.stok);
        $('#modalMenu').modal('show');
    }

    $('#formMenu').submit(function(e) {
        e.preventDefault();
        let data = {
            id: $('#m_id').val(),
            kode_barang: $('#m_kode').val(),
            nama_barang: $('#m_nama').val(),
            harga: $('#m_harga').val(),
            stok: $('#m_stok').val()
        };
        axios.post('/vendor/menu/store', data).then(res => {
            Swal.fire('Success', res.data.message, 'success').then(() => location.reload());
        });
    });

    function deleteData(id) {
        if(confirm('Delete this item?')) {
            axios.delete('/vendor/menu/delete/'+id).then(res => {
                location.reload();
            });
        }
    }
</script>
@endsection