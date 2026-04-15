@extends('layouts.main')
@section('content')
<div class="page-header">
    <h3 class="page-title">Data Customer</h3>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Customer</h4>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                                <th>Foto (Blob)</th>
                                <th>Foto (File)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $c)
                            <tr>
                                <td>{{ $c->id }}</td>
                                <td>{{ $c->nama }}</td>
                                <td>{{ $c->email ?? '-' }}</td>
                                <td>{{ $c->no_hp ?? '-' }}</td>
                                <td>{{ $c->alamat ?? '-' }}</td>
                                <td>
                                   @if($c->foto_blob)
                                        @php
                                            $blobData = is_resource($c->foto_blob) 
                                                ? stream_get_contents($c->foto_blob) 
                                                : $c->foto_blob;
                                        @endphp
                                        <img src="data:image/jpeg;base64,{{ base64_encode($blobData) }}" 
                                            width="60" height="60" style="object-fit:cover; border-radius:4px;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($c->foto_path)
                                        <img src="{{ Storage::url($c->foto_path) }}" 
                                             width="60" height="60" style="object-fit:cover; border-radius:4px;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection