@extends('layouts.main')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Sistem Kasir (Shopping Cart)</h4>
                
                <form id="formCari">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kode Barang</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="kode_barang" placeholder="P001">
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-gradient-primary" type="button" onclick="cekBarang()">Cek</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text" class="form-control" id="nama_barang" readonly>
                                <input type="hidden" id="id_produk"> </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Harga</label>
                                <input type="text" class="form-control" id="harga_barang" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" class="form-control" id="jumlah_beli" value="1" min="1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Aksi</label>
                                <button type="button" onclick="tambahKeKeranjang()" class="btn btn-block btn-gradient-info">Tambah</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="tabelKeranjang">
                            </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total Keseluruhan</th>
                                <th colspan="2" id="totalBelanja">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <button type="button" id="btnBayar" onclick="prosesSimpanSemua()" class="btn btn-gradient-success btn-lg" disabled>
                        <i class="mdi mdi-cash-multiple"></i> Bayar / Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

    let keranjang = []; 
    function cekBarang() {
    let kode = $('#kode_barang').val();
    if (!kode) return;

    let btn = $('.input-group-append button');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    axios.get('/get-barang/' + kode)
        .then(res => {
            $('#id_produk').val(res.data.id);
            $('#nama_barang').val(res.data.nama_produk);
            $('#harga_barang').val(res.data.harga_jual);
            $('#formCari .form-control').addClass('animate__animated animate__pulse');
            
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: '🛒 ' + res.data.nama_produk + ' ditemukan!'
            });
            setTimeout(() => {
                $('#formCari .form-control').removeClass('animate__animated animate__pulse');
            }, 1000);
            
            $('#jumlah_beli').focus();
        })
        .catch(err => {
            $('#kode_barang').addClass('animate__animated animate__shakeX');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Kode barang ' + kode + ' ga ada di rak!',
                showClass: { popup: 'animate__animated animate__fadeInDown' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp' }
            });
            setTimeout(() => $('#kode_barang').removeClass('animate__animated animate__shakeX'), 1000);
        })
        .finally(() => {
            btn.prop('disabled', false).html('Cek');
        });
}

    function tambahKeKeranjang() {
        let id = $('#id_produk').val();
        let kode = $('#kode_barang').val();
        let nama = $('#nama_barang').val();
        let harga = parseFloat($('#harga_barang').val());
        let jumlah = parseInt($('#jumlah_beli').val());

        if (!nama) {
            Swal.fire('Error', 'Cari barang dulu!', 'error');
            return;
        }

        let index = keranjang.findIndex(item => item.id === id);
        if (index !== -1) {
            keranjang[index].jumlah += jumlah;
            keranjang[index].subtotal = keranjang[index].jumlah * keranjang[index].harga;
        } else {
            keranjang.push({
                id: id,
                kode: kode,
                nama: nama,
                harga: harga,
                jumlah: jumlah,
                subtotal: harga * jumlah
            });
        }

        renderKeranjang();
        $('#formCari')[0].reset();
        $('#kode_barang').focus();
    }

    function renderKeranjang() {
        let html = '';
        let total = 0;

        keranjang.forEach((item, i) => {
            total += item.subtotal;
            html += `
                <tr>
                    <td>${item.kode}</td>
                    <td>${item.nama}</td>
                    <td>Rp ${item.harga.toLocaleString()}</td>
                    <td>${item.jumlah}</td>
                    <td>Rp ${item.subtotal.toLocaleString()}</td>
                    <td><button onclick="hapusItem(${i})" class="btn btn-danger btn-sm">x</button></td>
                </tr>
            `;
        });

        $('#tabelKeranjang').html(html);
        $('#totalBelanja').html('Rp ' + total.toLocaleString());
        $('#btnBayar').prop('disabled', keranjang.length === 0);
    }

    function hapusItem(index) {
        keranjang.splice(index, 1);
        renderKeranjang();
    }

    function prosesSimpanSemua() {
        let total = keranjang.reduce((acc, item) => acc + item.subtotal, 0);

        Swal.fire({
            title: 'Konfirmasi Bayar',
            text: "Total belanja: Rp " + total.toLocaleString(),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Simpan Transaksi'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/simpan-transaksi', {
                    items: keranjang, 
                    total: total
                })
                .then(res => {
                    Swal.fire('Berhasil!', 'Transaksi Selesai.', 'success');
                    keranjang = [];
                    renderKeranjang();
                })
                .catch(err => {
                    Swal.fire('Gagal', 'Ada masalah saat simpan', 'error');
                });
            }
        });
    }
</script>
@endsection