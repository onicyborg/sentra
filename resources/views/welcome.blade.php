@extends('layouts.master')

@section('page_title', 'Dashboard')

@section('content')
    <div class="mb-5">
        <h1 class="fw-bold fs-2qx mb-3">Dashboard SENTRA</h1>
        <div class="text-gray-600">Sistem Elektronik Naskah & Arsip</div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2"><i class="bi bi-people me-2"></i>Total Users</div>
                    <div class="fs-2 fw-bold">{{ $usersCount ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2"><i class="bi bi-shield-lock me-2"></i>Total Roles</div>
                    <div class="fs-2 fw-bold">{{ $rolesCount ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2"><i class="bi bi-key me-2"></i>Total Permissions</div>
                    <div class="fs-2 fw-bold">{{ $permissionsCount ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2"><i class="bi bi-archive me-2"></i>Total Arsip</div>
                    <div class="fs-2 fw-bold">{{ $arsipCount ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2"><i class="bi bi-inbox me-2"></i>Total Surat Masuk</div>
                    <div class="fs-2 fw-bold">{{ $suratMasukCount ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2"><i class="bi bi-send me-2"></i>Total Surat Keluar</div>
                    <div class="fs-2 fw-bold">{{ $suratKeluarCount ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2"><i class="bi bi-archive me-2"></i>Total Arsip</div>
                    <div class="fs-2 fw-bold">{{ $arsipCount ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="card card-flush">
        <div class="card-header">
            <div class="card-title">
                <h3 class="fw-bold">Aktivitas Terbaru</h3>
            </div>
        </div>
        <div class="card-body py-5">
            @if(!empty($activities ?? []) && count($activities))
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-gray-500 fw-semibold text-uppercase gs-0">
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Method</th>
                            <th>URL</th>
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-700">
                        @foreach($activities as $a)
                            <tr>
                                <td>{{ optional($a->created_at)->format('d M Y H:i') }}</td>
                                <td>{{ optional($a->user)->name ?? '-' }}</td>
                                <td>{{ $a->method }}</td>
                                <td class="text-truncate" style="max-width: 420px;">
                                    {{ $a->url }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted">Belum ada aktivitas.</div>
            @endif
        </div>
    </div>
@endsection
