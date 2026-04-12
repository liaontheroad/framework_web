@extends('layouts.main')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title text-primary"><i class="mdi mdi-cart"></i> Sistem Kasir Modern</h4>
                <hr>
                
                <form id="formCari" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">Kode Barang</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="kode_barang" placeholder="Contoh: P001">
                                    <div class="input-group-append">
                                        <button class="btn btn-gradient-primary" type="button" id="btnCek" onclick="cekBarang()">
                                            Cek
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">Nama Barang</label>
                                <input type="text" class="form-control bg-light" id="nama_barang" readonly>
                                <input type="hidden" id="id_produk">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold">Harga</label>
                                <input type="text" class="form-control bg-light" id="harga_barang" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah_beli" value="1" min="1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="btnTambah" onclick="tambahKeKeranjang()" class="btn btn-block btn-gradient-info">
                                    <i class="mdi mdi-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Harga Satuan</th>
                                <th width="100">Qty</th>
                                <th>Subtotal</th>
                                <th width="50">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelKeranjang">
                            </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="4" class="text-right h5">Total Pembayaran:</th>
                                <th colspan="2" id="totalBelanja" class="h5 text-danger font-weight-bold">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <button type="button" id="btnBayar" onclick="prosesSimpanSemua()" class="btn btn-gradient-success btn-lg px-5" disabled>
                        <i class="mdi mdi-cash-register"></i> BAYAR SEKARANG
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

    let btn = $('#btnCek');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    axios.get('/get-barang/' + kode)
        .then(res => {
            // Isi data ke input
            $('#id_produk').val(res.data.id); 
            $('#nama_barang').val(res.data.nama_produk); 
            $('#harga_barang').val(res.data.harga);      
            
            // --- DI SINI TEMPATNYA ---
            // Barang ditemukan, maka aktifkan tombol tambah (Ketentuan d)
            $('#btnTambah').prop('disabled', false);

            $('#formCari .form-control').addClass('animate__animated animate__pulse');
            $('#jumlah_beli').focus().select();
        })
        .catch(err => {
            // --- DI SINI JUGA ---
            // Jika error (tidak ditemukan), pastikan tombol tambah tetap mati
            $('#btnTambah').prop('disabled', true);
            
            Swal.fire('Oops!', 'Kode barang tidak terdaftar!', 'error');
        })
        .finally(() => {
            btn.prop('disabled', false).html('Cek');
        });
}
        let kode = $('#kode_barang').val();
        if (!kode) return;

        let btn = $('#btnCek');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        axios.get('/get-barang/' + kode)
            .then(res => {
                // DISESUAIKAN DENGAN NAMA KOLOM SQL BARU
                $('#id_produk').val(res.data.id); 
                $('#nama_barang').val(res.data.nama_produk); // Kolom db: nama_produk
                $('#harga_barang').val(res.data.harga);      // Kolom db: harga
                
                $('#formCari .form-control').addClass('animate__animated animate__pulse');
                // ... (rest of toast)
                $('#jumlah_beli').focus().select();
            })
            .catch(err => {
                // ... (error handling)
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

        if (!nama || jumlah < 1) {
            Swal.fire('Perhatian', 'Cari barang dulu dan pastikan jumlah minimal 1!', 'warning');
            return;
        }

        let index = keranjang.findIndex(item => item.id === id);
        if (index !== -1) {
            keranjang[index].jumlah += jumlah;
            keranjang[index].subtotal = keranjang[index].jumlah * keranjang[index].harga;
        } else {
            keranjang.push({ id, kode, nama, harga, jumlah, subtotal: harga * jumlah });
        }

        renderKeranjang();
        $('#formCari')[0].reset();
        $('#btnTambah').prop('disabled', true); 
        $('#kode_barang').focus();
    }

    function renderKeranjang() {
        let html = '';
        let total = 0;

        keranjang.forEach((item, i) => {
            total += item.subtotal;
            html += `
                <tr class="animate__animated animate__fadeInDown">
                    <td class="text-center">${item.kode}</td>
                    <td>${item.nama}</td>
                    <td class="text-right">Rp ${item.harga.toLocaleString()}</td>
                    <td class="text-center">${item.jumlah}</td>
                    <td class="text-right">Rp ${item.subtotal.toLocaleString()}</td>
                    <td class="text-center">
                        <button onclick="hapusItem(${i})" class="btn btn-danger btn-xs">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>`;
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
            title: 'Konfirmasi Pembayaran',
            text: "Total yang harus dibayar: Rp " + total.toLocaleString(),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Proses & Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                axios.post('/simpan-transaksi', {
                    items: keranjang,
                    total: total
                })
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pembayaran transaksi berhasil disimpan',
                        timer: 2500,
                        showConfirmButton: false
                    });

                    keranjang = [];
                    renderKeranjang();
                    $('#formCari')[0].reset();
                })
                .catch(err => {
                    Swal.fire('Gagal!', 'Ada masalah saat menyimpan transaksi ke server.', 'error');
                });
            }
        });
    }

    $('#kode_barang').keypress(function (e) {
        if (e.which == 13) {
            e.preventDefault();
            cekBarang();
        }
    });
</script>
@endsection