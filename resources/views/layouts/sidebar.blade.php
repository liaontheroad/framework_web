<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile">
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
          <span class="text-secondary text-small">{{ ucfirst(Auth::user()->role->name ?? 'User') }}</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>

    <li class="nav-item {{ Request::is('home') ? 'active' : '' }}">
      <a class="nav-link" href="/home">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    @if(Auth::user()->role?->name == 'admin')
      
      <li class="nav-item {{ Request::is('kategori*') || Request::is('buku*') ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#data-master" aria-expanded="{{ Request::is('kategori*') || Request::is('buku*') ? 'true' : 'false' }}" aria-controls="data-master">
          <span class="menu-title">Data Master</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-database menu-icon"></i>
        </a>
        <div class="collapse {{ Request::is('kategori*') || Request::is('buku*') ? 'show' : '' }}" id="data-master">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> 
              <a class="nav-link {{ Request::is('kategori*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">Kelola Kategori</a>
            </li>
            <li class="nav-item"> 
              <a class="nav-link {{ Request::is('buku*') ? 'active' : '' }}" href="{{ route('buku.index') }}">Kelola Buku</a>
            </li>
          </ul>
        </div>
      </li>

      <li class="nav-item {{ Request::is('barang*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('barang.index') }}">
          <span class="menu-title">Tag Harga UMKM</span>
          <i class="mdi mdi-tag menu-icon"></i>
        </a>
      </li>

      <li class="nav-item {{ Request::is('modul-4*') ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#modul-empat" aria-expanded="{{ Request::is('modul-4*') ? 'true' : 'false' }}" aria-controls="modul-empat">
          <span class="menu-title">Modul 4</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-flask menu-icon"></i> 
        </a>
        <div class="collapse {{ Request::is('modul-4*') ? 'show' : '' }}" id="modul-empat">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> 
              <a class="nav-link {{ Request::routeIs('modul4.biasa') ? 'active' : '' }}" href="{{ route('modul4.biasa') }}">Tabel Biasa</a>
            </li>
            
            <li class="nav-item"> 
              <a class="nav-link {{ Request::routeIs('modul4.datatables') ? 'active' : '' }}" href="{{ route('modul4.datatables') }}">Tabel DataTables</a>
            </li>

            <li class="nav-item"> 
              <a class="nav-link {{ Request::routeIs('modul4.select2') ? 'active' : '' }}" href="{{ route('modul4.select2') }}">Select2 Kota</a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item {{ Request::is('modul-ajax*') ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#modul-ajax" aria-expanded="{{ Request::is('modul-ajax*') ? 'true' : 'false' }}" aria-controls="modul-ajax">
          <span class="menu-title">Modul AJAX & Axios</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi- satellite-variant menu-icon"></i> 
        </a>
        <div class="collapse {{ Request::is('modul-ajax*') ? 'show' : '' }}" id="modul-ajax">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> 
              <a class="nav-link {{ Request::routeIs('wilayah.index') ? 'active' : '' }}" href="{{ route('wilayah.index') }}">Manajemen Wilayah (Ajax)</a>
            </li>
            <li class="nav-item"> 
              <a class="nav-link {{ Request::routeIs('wilayah.axios') ? 'active' : '' }}" href="{{ route('wilayah.axios') }}">Manajemen Wilayah (Axios)</a>
            </li>
            <li class="nav-item"> 
              <a class="nav-link {{ Request::routeIs('kasir.index') ? 'active' : '' }}" href="{{ route('kasir.index') }}">Aplikasi Kasir (Ajax)</a>
            </li>
            <li class="nav-item"> 
              <a class="nav-link {{ Request::is('kasir-axios') ? 'active' : '' }}" href="{{ route('kasir.axios') }}">Aplikasi Kasir (Axios)</a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#cetak-pdf-admin" aria-expanded="false" aria-controls="cetak-pdf-admin">
          <span class="menu-title">Cetak Laporan PDF</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-printer menu-icon"></i>
        </a>
        <div class="collapse" id="cetak-pdf-admin">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> 
              <a class="nav-link" href="/cetak-landscape" target="_blank">Cetak Landscape</a>
            </li>
            <li class="nav-item"> 
              <a class="nav-link" href="/cetak-potrait" target="_blank">Cetak Potrait</a>
            </li>
          </ul>
        </div>
      </li>
    <li class="nav-item {{ Request::is('customer*') ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#menu-customer" 
          aria-expanded="{{ Request::is('customer*') ? 'true' : 'false' }}">
            <span class="menu-title">Customer</span>
            <i class="menu-arrow"></i>
            <i class="mdi mdi-account-group menu-icon"></i>
        </a>
        <div class="collapse {{ Request::is('customer*') ? 'show' : '' }}" id="menu-customer">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('customer') ? 'active' : '' }}" 
                      href="{{ route('customer.index') }}">Data Customer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('customer/tambah1') ? 'active' : '' }}" 
                      href="{{ route('customer.tambah1') }}">Tambah Customer 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('customer/tambah2') ? 'active' : '' }}" 
                      href="{{ route('customer.tambah2') }}">Tambah Customer 2</a>
                </li>
            </ul>
        </div>
    </li>
    @endif

    @if(Auth::user()->role?->name == 'visitor')

      <li class="nav-item {{ Request::is('visitor/kategori*') || Request::is('visitor/buku*') ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#data-visitor" aria-expanded="{{ Request::is('visitor/kategori*') || Request::is('visitor/buku*') ? 'true' : 'false' }}" aria-controls="data-visitor">
          <span class="menu-title">Data Perpustakaan</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-book-open-page-variant menu-icon"></i>
        </a>
        <div class="collapse {{ Request::is('visitor/kategori*') || Request::is('visitor/buku*') ? 'show' : '' }}" id="data-visitor">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> 
              <a class="nav-link {{ Request::is('visitor/kategori*') ? 'active' : '' }}" href="{{ route('visitor.kategori') }}">Daftar Kategori</a>
            </li>
            <li class="nav-item"> 
              <a class="nav-link {{ Request::is('visitor/buku*') ? 'active' : '' }}" href="{{ route('visitor.buku') }}">Daftar Buku</a>
            </li>
          </ul>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#cetak-pdf-visitor" aria-expanded="false" aria-controls="cetak-pdf-visitor">
          <span class="menu-title">Cetak Laporan PDF</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-printer menu-icon"></i>
        </a>
        <div class="collapse" id="cetak-pdf-visitor">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> 
              <a class="nav-link" href="/cetak-landscape" target="_blank">Cetak Landscape</a>
            </li>
            <li class="nav-item"> 
              <a class="nav-link" href="/cetak-potrait" target="_blank">Cetak Potrait</a>
            </li>
          </ul>
        </div>
      </li>

    @endif

    @if(Auth::user()->role?->name == 'pemilik_vendor')

    <li class="nav-item {{ Request::is('vendor/menu*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('vendor.menu') }}">
            <span class="menu-title">Kelola Menu</span>
            <i class="mdi mdi-food menu-icon"></i>
        </a>
    </li>

    <li class="nav-item {{ Request::is('vendor/pesanan*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('vendor.pesanan') }}">
            <span class="menu-title">Pesanan Masuk</span>
            <i class="mdi mdi-receipt menu-icon"></i>
        </a>
    </li>
    <li class="nav-item {{ Request::is('vendor/scan-qr') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('vendor.scan') }}">
            <span class="menu-title">Scan QR Code</span>
            <i class="mdi mdi-qrcode-scan menu-icon"></i>
        </a>
    </li>

@endif
  </ul>
</nav>