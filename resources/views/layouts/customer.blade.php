<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kantin Online - Guest Mode</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* Menyesuaikan layout karena tidak ada sidebar */
        .main-panel-customer {
            width: 100%;
            transition: width 0.25s ease, margin 0.25s ease;
            min-height: calc(100vh - 70px);
            display: flex;
            flex-direction: column;
        }
        .content-wrapper-customer {
            padding: 2.75rem 2.25rem;
            width: 100%;
            flex-grow: 1;
        }
        .navbar-customer {
            background: #fff;
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            box-shadow: 0px 1px 15px 1px rgba(69, 65, 78, 0.08);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-scroller">
        <nav class="navbar-customer justify-content-between">
            <a class="navbar-brand brand-logo" href="/">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" height="30" />
            </a>
            <div>
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Vendor Login</a>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">
            <div class="main-panel-customer">
                <div class="content-wrapper-customer">
                    @yield('content')
                </div>
                
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                            Copyright © 2024 <a href="#" target="_blank">Kantin Vokasi</a>. All rights reserved.
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    @stack('scripts')
</body>
</html>