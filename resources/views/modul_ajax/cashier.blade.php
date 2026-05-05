@extends('layouts.customer')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="card-title text-info"><i class="mdi mdi-shopping"></i> Online Cashier System</h4>
                <p class="card-description">Welcome! Please choose a vendor and start your order.</p>
                <hr>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="font-weight-bold">1. Select Vendor</label>
                        <select id="vendor_id" class="form-control" onchange="updateMenu()">
                            <option value="">-- Choose Vendor --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">2. Choose Menu</label>
                        <select id="menu_id" class="form-control" onchange="setPrice()">
                            <option value="">-- Choose Menu --</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="font-weight-bold">Price</label>
                        <input type="text" id="harga_barang" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="font-weight-bold">Quantity</label>
                        <input type="number" id="qty_input" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button class="btn btn-info btn-block" onclick="addToCart()">Add</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th width="80">Qty</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cartTable"></tbody>
                        <tfoot class="bg-light h5">
                            <tr>
                                <th colspan="3" class="text-right">Grand Total:</th>
                                <th id="grandTotal" class="text-danger font-weight-bold">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <button id="btnCheckout" class="btn btn-success btn-lg px-5" onclick="checkout()" disabled>
                        <i class="mdi mdi-check-circle"></i> PLACE ORDER
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/sweetalert2@11"></script>
<script type="text/javascript"
  src="https://app.sandbox.midtrans.com/snap/snap.js"
  data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
</script>

<script>
let cart = [];

// On page load, check localStorage for saved QR
$(document).ready(function() {
    let saved = localStorage.getItem('last_qr');
    if (saved) {
        let data = JSON.parse(saved);
        $('div.card-body').prepend(`
            <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                <span>
                    <i class="mdi mdi-qrcode"></i>
                    Anda memiliki pesanan aktif: <strong>${data.nomor_faktur}</strong>
                </span>
                <div>
                    <button class="btn btn-info btn-sm mr-2" onclick="lihatQR()">
                        <i class="mdi mdi-qrcode-scan"></i> Lihat QR Saya
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="hapusQR()">
                        Hapus
                    </button>
                </div>
            </div>
        `);
    }
});

function showQR(nomor_faktur, qr_code) {
    Swal.fire({
        title: 'Pembayaran Berhasil!',
        html: `
            <p>No. Pesanan: <strong>${nomor_faktur}</strong></p>
            <p>Tunjukkan QR Code ini ke kasir:</p>
            <img src="${qr_code}"
                style="width:200px; height:200px; display:block; margin:0 auto;">
            <br>
            <small class="text-muted">QR Code tersimpan. Klik "Lihat QR Saya" jika perlu melihat lagi.</small>
        `,
        icon: 'success',
        confirmButtonText: 'Selesai'
    }).then(() => {
        location.reload();
    });
}

function lihatQR() {
    let data = JSON.parse(localStorage.getItem('last_qr'));
    showQR(data.nomor_faktur, data.qr_code);
}

function hapusQR() {
    localStorage.removeItem('last_qr');
    location.reload();
}

function updateMenu() {
    let vId = $('#vendor_id').val();
    if(!vId) return $('#menu_id').html('<option value="">-- Choose Menu --</option>');
    axios.get('/get-menu-vendor/' + vId).then(res => {
        let options = '<option value="">-- Choose Menu --</option>';
        res.data.forEach(m => {
            options += `<option value="${m.id}" data-harga="${m.harga}" data-nama="${m.nama_makanan}">${m.nama_makanan}</option>`;
        });
        $('#menu_id').html(options);
    });
}

function setPrice() {
    let price = $('#menu_id option:selected').data('harga');
    $('#harga_barang').val(price || 0);
}

function addToCart() {
    let id = $('#menu_id').val();
    let nama = $('#menu_id option:selected').data('nama');
    let harga = parseInt($('#harga_barang').val());
    let qty = parseInt($('#qty_input').val());
    if(!id || qty < 1) return Swal.fire('Error', 'Pilih menu & jumlah!', 'error');

    let index = cart.findIndex(item => item.id == id);
    if(index > -1) {
        cart[index].qty += qty;
        cart[index].subtotal = cart[index].qty * cart[index].harga;
    } else {
        cart.push({ id, nama, harga, qty, subtotal: harga * qty });
    }
    renderCart();
}

function renderCart() {
    let html = ''; let total = 0;
    cart.forEach((item, i) => {
        total += item.subtotal;
        html += `<tr>
            <td>${item.nama}</td>
            <td>Rp ${item.harga.toLocaleString()}</td>
            <td>
                <div class="input-group input-group-sm" style="width:100px">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary btn-sm" onclick="changeQty(${i}, -1)">-</button>
                    </div>
                    <input type="number" class="form-control text-center" value="${item.qty}" min="1" onchange="setQty(${i}, this.value)">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-sm" onclick="changeQty(${i}, 1)">+</button>
                    </div>
                </div>
            </td>
            <td>Rp ${item.subtotal.toLocaleString()}</td>
            <td><button class="btn btn-danger btn-sm" onclick="removeFromCart(${i})">x</button></td>
        </tr>`;
    });
    $('#cartTable').html(html);
    $('#grandTotal').text('Rp ' + total.toLocaleString());
    $('#btnCheckout').prop('disabled', cart.length === 0);
}

function changeQty(i, delta) {
    cart[i].qty += delta;
    if (cart[i].qty < 1) cart[i].qty = 1;
    cart[i].subtotal = cart[i].qty * cart[i].harga;
    renderCart();
}

function setQty(i, val) {
    let qty = parseInt(val);
    if (qty < 1 || isNaN(qty)) qty = 1;
    cart[i].qty = qty;
    cart[i].subtotal = cart[i].qty * cart[i].harga;
    renderCart();
}

function removeFromCart(i) {
    cart.splice(i, 1);
    renderCart();
}

function checkout() {
    if (cart.length === 0) return;

    let btn = $('#btnCheckout');
    btn.prop('disabled', true).text('Processing...');

    let cleanItems = Object.values(cart).map(item => ({
        makanan_id: item.id,
        qty: parseInt(item.qty),
        harga: parseInt(item.harga),
        subtotal: parseInt(item.subtotal)
    }));

    let totalAmount = cleanItems.reduce((a, b) => a + b.subtotal, 0);

    axios.post('/simpan-pesanan', {
        items: cleanItems,
        total: totalAmount
    })
    .then(res => {
        window.snap.pay(res.data.snap_token, {
            onSuccess: function(result) {
                axios.post('/konfirmasi-bayar/' + res.data.order_id)
                .then(response => {
                    // Save QR to localStorage so customer can access it after reload
                    localStorage.setItem('last_qr', JSON.stringify({
                        nomor_faktur: response.data.nomor_faktur,
                        qr_code: response.data.qr_code
                    }));

                    showQR(response.data.nomor_faktur, response.data.qr_code);
                });
            },
            onPending: function(result) {
                Swal.fire('Pending', 'Selesaikan pembayaran kamu.', 'info');
            },
            onClose: function() {
                btn.prop('disabled', false).text('PLACE ORDER');
            }
        });
    })
    .catch(err => {
        btn.prop('disabled', false).text('PLACE ORDER');
        Swal.fire('Error', err.response?.data?.message || 'Server Gagal Terhubung', 'error');
    });
}
</script>
@endsection