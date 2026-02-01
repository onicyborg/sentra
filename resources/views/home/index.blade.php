@extends('layouts.master')

@section('page_title', 'Home')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-7">
        <h3 class="fw-bold mb-0">Selamat datang</h3>
    </div>

    <div class="row g-6 g-xl-9">
        @canany(['surat_masuk.create','surat_masuk.read'])
        <div class="col-md-6 col-xl-4">
            <a href="{{ url('/surat-masuk') }}" class="card hover-elevate-up shadow-sm h-100 text-decoration-none">
                <div class="card-body d-flex align-items-center">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label bg-light-primary">
                            <i class="bi bi-inbox fs-2 text-primary"></i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-gray-900">Surat Masuk</div>
                        <div class="text-muted">Daftar & kelola surat masuk</div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        @canany(['surat_keluar.create','surat_keluar.read'])
        <div class="col-md-6 col-xl-4">
            <a href="{{ url('/surat-keluar') }}" class="card hover-elevate-up shadow-sm h-100 text-decoration-none">
                <div class="card-body d-flex align-items-center">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label bg-light-success">
                            <i class="bi bi-send fs-2 text-success"></i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-gray-900">Surat Keluar</div>
                        <div class="text-muted">Daftar & kelola surat keluar</div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        @can('surat_masuk.follow_up')
        <div class="col-md-6 col-xl-4">
            <a href="{{ url('/tindak-lanjut') }}" class="card hover-elevate-up shadow-sm h-100 text-decoration-none">
                <div class="card-body d-flex align-items-center">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label bg-light-warning">
                            <i class="bi bi-journal-check fs-2 text-warning"></i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-gray-900">Tindak Lanjut</div>
                        <div class="text-muted">Daftar tugas dan tindak lanjut</div>
                    </div>
                </div>
            </a>
        </div>
        @endcan

        <div class="col-md-6 col-xl-4">
            <a href="{{ url('/notifications') }}" class="card hover-elevate-up shadow-sm h-100 text-decoration-none">
                <div class="card-body d-flex align-items-center">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label bg-light-info">
                            <i class="bi bi-bell fs-2 text-info"></i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-gray-900">Notifikasi</div>
                        <div class="text-muted">Pemberitahuan terbaru</div>
                    </div>
                </div>
            </a>
        </div>

        @can('archive.read')
        <div class="col-md-6 col-xl-4">
            <a href="{{ url('/arsip') }}" class="card hover-elevate-up shadow-sm h-100 text-decoration-none">
                <div class="card-body d-flex align-items-center">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label bg-light-secondary">
                            <i class="bi bi-archive fs-2 text-secondary"></i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-gray-900">Arsip</div>
                        <div class="text-muted">Akses arsip surat</div>
                    </div>
                </div>
            </a>
        </div>
        @endcan
    </div>
@endsection
