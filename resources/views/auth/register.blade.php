@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6"> <div class="card shadow-sm border-0 rounded-lg mt-5"> <div class="card-header bg-white text-center pt-4 pb-2 border-0">
                    <h3 class="font-weight-bold text-primary">Buat Akun Baru</h3>
                    <p class="text-muted font-weight-light">Bergabunglah dan nikmati fasilitas kami</p>
                </div>

                <div class="card-body px-5 pb-5">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name" class="text-muted">{{ __('Nama Lengkap') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend bg-transparent">
                                    <span class="input-group-text bg-transparent border-right-0">
                                        <i class="mdi mdi-account-outline text-primary"></i>
                                    </span>
                                </div>
                                <input id="name" type="text" class="form-control form-control-lg border-left-0 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Masukkan nama Anda">
                                
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="text-muted">{{ __('Alamat Email') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend bg-transparent">
                                    <span class="input-group-text bg-transparent border-right-0">
                                        <i class="mdi mdi-email-outline text-primary"></i>
                                    </span>
                                </div>
                                <input id="email" type="email" class="form-control form-control-lg border-left-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="contoh@email.com">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="text-muted">{{ __('Password') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend bg-transparent">
                                    <span class="input-group-text bg-transparent border-right-0">
                                        <i class="mdi mdi-lock-outline text-primary"></i>
                                    </span>
                                </div>
                                <input id="password" type="password" class="form-control form-control-lg border-left-0 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="password-confirm" class="text-muted">{{ __('Konfirmasi Password') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend bg-transparent">
                                    <span class="input-group-text bg-transparent border-right-0">
                                        <i class="mdi mdi-lock-check-outline text-primary"></i>
                                    </span>
                                </div>
                                <input id="password-confirm" type="password" class="form-control form-control-lg border-left-0" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password">
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn w-100" style="border-radius: 8px;">
                                {{ __('DAFTAR SEKARANG') }}
                            </button>
                        </div>
                        
                        <div class="text-center mt-4 font-weight-light">
                            Sudah punya akun? <a href="{{ route('login') }}" class="text-primary font-weight-bold">Login di sini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection