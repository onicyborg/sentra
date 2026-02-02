@extends('layouts.master')

@section('page_title', 'Laporan Manajerial')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <h3 class="fw-bold mb-0">Laporan Manajerial</h3>
</div>

<!-- Filter -->
<div class="card mb-6">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.index') }}" id="reportFilterForm">
            <div class="row g-4 align-items-end">

                {{-- FILTER --}}
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" class="form-control"
                        name="from_date"
                        value="{{ $filters['from_date'] ?? '' }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sampai Tanggal</label>
                    <input type="date" class="form-control"
                        name="to_date"
                        value="{{ $filters['to_date'] ?? '' }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Jenis Surat</label>
                    <select name="jenis" class="form-select">
                        <option value="semua" {{ ($filters['jenis'] ?? '') === 'semua' ? 'selected' : '' }}>
                            Semua
                        </option>
                        <option value="masuk" {{ ($filters['jenis'] ?? '') === 'masuk' ? 'selected' : '' }}>
                            Surat Masuk
                        </option>
                        <option value="keluar" {{ ($filters['jenis'] ?? '') === 'keluar' ? 'selected' : '' }}>
                            Surat Keluar
                        </option>
                    </select>
                </div>

                {{-- ACTIONS --}}
                <div class="col-md-3">
                    <div class="d-flex flex-column gap-3">

                        {{-- Filter Actions --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                Tampilkan
                            </button>

                            <a href="{{ route('reports.index') }}"
                               class="btn btn-light">
                                Reset
                            </a>
                        </div>

                        {{-- Export Actions --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('reports.export.pdf', [
                                    'from_date' => $filters['from_date'] ?? '',
                                    'to_date'   => $filters['to_date'] ?? '',
                                    'jenis'     => $filters['jenis'] ?? 'semua'
                                ]) }}"
                               target="_blank"
                               class="btn btn-danger flex-grow-1">
                                Export PDF
                            </a>

                            <a href="{{ route('reports.export.excel', [
                                    'from_date' => $filters['from_date'] ?? '',
                                    'to_date'   => $filters['to_date'] ?? '',
                                    'jenis'     => $filters['jenis'] ?? 'semua'
                                ]) }}"
                               class="btn btn-success flex-grow-1">
                                Export Excel
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </form>
    </div>
</div>


<!-- Summary Cards -->
<div class="row g-6 mb-6">
    <div class="col-md-3">
        <div class="card card-flush h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted">Total Surat Masuk</div>
                <div class="fs-2hx fw-bold">{{ $summary['total_masuk'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-flush h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted">Total Surat Keluar</div>
                <div class="fs-2hx fw-bold">{{ $summary['total_keluar'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-flush h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted">Total Arsip</div>
                <div class="fs-2hx fw-bold">{{ $summary['total_arsip'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-flush h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted">Total Surat Diproses</div>
                <div class="fs-2hx fw-bold">{{ $summary['total_diproses'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Rekap: Belum Diarsip -->
<div class="card mb-6">
    <div class="card-header">
        <h3 class="card-title fw-bold">Belum Diarsip</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="report_table_unarchived">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>Jenis</th>
                        <th>Nomor Surat</th>
                        <th>Tanggal Surat</th>
                        <th>Pengirim/Tujuan</th>
                        <th>Perihal</th>
                        <th>Status Akhir</th>
                        <th class="text-end w-100px">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-semibold">
                    @foreach ($rekap_unarchived as $r)
                        <tr>
                            <td class="text-capitalize">{{ $r->jenis }}</td>
                            <td>{{ $r->nomor_surat ?? '-' }}</td>
                            <td>{{ $r->tanggal_surat ? \Carbon\Carbon::parse($r->tanggal_surat)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $r->pihak ?? '-' }}</td>
                            <td>{{ $r->perihal ?? '-' }}</td>
                            <td>{{ $r->status ?? '-' }}</td>
                            <td class="text-end">
                                <button class="btn btn-light-info btn-sm btn-view" data-jenis="{{ $r->jenis }}" data-id="{{ $r->surat_id }}" data-bs-toggle="modal" data-bs-target="#detailModal">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>

<!-- Rekap: Sudah Diarsip -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title fw-bold">Sudah Diarsip</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="report_table_archived">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>Jenis</th>
                        <th>Nomor Surat</th>
                        <th>Tanggal Surat</th>
                        <th>Pengirim/Tujuan</th>
                        <th>Perihal</th>
                        <th>Status Akhir</th>
                        <th>Tanggal Arsip</th>
                        <th class="text-end w-100px">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-semibold">
                    @foreach ($rekap_archived as $r)
                        <tr>
                            <td class="text-capitalize">{{ $r->jenis }}</td>
                            <td>{{ $r->nomor_surat ?? '-' }}</td>
                            <td>{{ $r->tanggal_surat ? \Carbon\Carbon::parse($r->tanggal_surat)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $r->pihak ?? '-' }}</td>
                            <td>{{ $r->perihal ?? '-' }}</td>
                            <td>{{ $r->status ?? '-' }}</td>
                            <td>{{ $r->archived_at ? \Carbon\Carbon::parse($r->archived_at)->format('Y-m-d H:i') : '-' }}</td>
                            <td class="text-end">
                                <button class="btn btn-light-info btn-sm btn-view" data-jenis="{{ $r->jenis }}" data-id="{{ $r->surat_id }}" data-bs-toggle="modal" data-bs-target="#detailModal">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal (reuse) -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Surat</h5>
                <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body" id="report_detail_body" style="max-height:70vh;overflow-y:auto;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#report_table_unarchived').DataTable({
        pageLength: 10,
        ordering: true,
        language: { emptyTable: 'Tidak ada data' }
    });
    $('#report_table_archived').DataTable({
        pageLength: 10,
        ordering: true,
        language: { emptyTable: 'Tidak ada data' }
    });
});

(function(){
    const body = document.getElementById('report_detail_body');
    function badgeMasuk(status){
        const s=(status||'').toLowerCase();
        if (s==='draft') return '<span class="badge badge-light-secondary">Draft</span>';
        if (s==='diterima') return '<span class="badge badge-light-info">Diterima</span>';
        if (s==='terverifikasi') return '<span class="badge badge-light-success">Terverifikasi</span>';
        if (s==='didisposisikan') return '<span class="badge badge-light-primary">Didisposisikan</span>';
        if (s==='ditindaklanjuti') return '<span class="badge badge-light-success">Ditindaklanjuti</span>';
        return `<span class="badge badge-light">${status||'-'}</span>`;
    }
    function badgeKeluar(status){
        const s=(status||'').toLowerCase();
        if (s==='draft') return '<span class="badge badge-light-secondary">Draft</span>';
        if (s==='disahkan') return '<span class="badge badge-light-success">Disahkan</span>';
        if (s==='terkirim') return '<span class="badge badge-light-primary">Terkirim</span>';
        if (s==='ditolak') return '<span class="badge badge-light-danger">Ditolak</span>';
        return `<span class="badge badge-light">${status||'-'}</span>`;
    }
    function renderMasuk(d){
        const info = `
            <div class=\"card mb-5\"><div class=\"card-body\"><div class=\"row g-3\">
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Nomor Surat</div><div class=\"fw-bold\">${d.nomor_surat||'-'}</div></div>
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Tanggal Terima</div><div class=\"fw-bold\">${(d.tanggal_terima||'').substring(0,10)}</div></div>
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Asal Surat</div><div class=\"fw-bold\">${d.asal_surat||'-'}</div></div>
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Pengirim</div><div class=\"fw-bold\">${d.pengirim||'-'}</div></div>
                <div class=\"col-12\"><div class=\"text-muted fs-8\">Perihal</div><div class=\"fw-bold\">${d.perihal||'-'}</div></div>
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Status</div><div class=\"fw-bold\">${badgeMasuk(d.status)}</div></div>
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Dibuat oleh</div><div class=\"fw-bold\">${d.created_by_name||'-'}</div></div>
            </div></div></div>`;
        const f=d.flow||{};
        const timeline = `
            <div class=\"timeline\">
                <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-inbox\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Surat Diterima</div><div class=\"text-muted fs-8\">Tanggal terima: ${(d.tanggal_terima||'').substring(0,10)} | Pengirim: ${d.pengirim||'-'}</div></div></div>
                <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-check-circle\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Verifikasi <span class=\"ms-2 badge ${f.verifikasi?.status==='completed'?'badge-light-success':'badge-light-warning'}\">${f.verifikasi?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Tanggal: ${f.verifikasi?.verified_at?String(f.verifikasi.verified_at).substring(0,10):'-'}</div></div></div>
                <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-share\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Disposisi <span class=\"ms-2 badge ${f.disposisi?.status==='completed'?'badge-light-primary':'badge-light-warning'}\">${f.disposisi?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Tanggal: ${f.disposisi?.tanggal?String(f.disposisi.tanggal).substring(0,10):'-'} | Catatan: ${f.disposisi?.catatan||'-'}</div></div></div>
                <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-clipboard-check\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Tindak Lanjut <span class=\"ms-2 badge ${f.tindak_lanjut?.status==='completed'?'badge-light-success':'badge-light-warning'}\">${f.tindak_lanjut?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Tanggal: ${f.tindak_lanjut?.tanggal?String(f.tindak_lanjut.tanggal).substring(0,10):'-'} | Deskripsi: ${f.tindak_lanjut?.deskripsi||'-'}</div></div></div>
                <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-archive\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Arsip <span class=\"ms-2 badge ${f.arsip?.status==='completed'?'badge-light-success':'badge-light-warning'}\">${f.arsip?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Tanggal arsip: ${f.arsip?.archived_at?String(f.arsip.archived_at).substring(0,10):'-'}</div></div></div>
            </div>`;
        const lampSurat = (d.lampiran_surat||[]).map(l=>`<a class=\"d-block\" href=\"${l.url}\" target=\"_blank\">${l.name}</a>`).join('');
        const lampTL = (d.lampiran_tindak_lanjut||[]).map(l=>`<a class=\"d-block\" href=\"${l.url}\" target=\"_blank\">${l.name}</a>`).join('');
        body.innerHTML = info + `<div class=\"card\"><div class=\"card-body\">${timeline}
            <div class=\"mt-5\"><div class=\"fw-bold mb-2\">Lampiran Surat</div>${lampSurat||'-'}</div>
            <div class=\"mt-5\"><div class=\"fw-bold mb-2\">Lampiran Tindak Lanjut</div>${lampTL||'-'}</div>
        </div></div>`;
    }
    function renderKeluar(d){
        const info = `
            <div class=\"card mb-5\"><div class=\"card-body\"><div class=\"row g-3\">
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Nomor Surat</div><div class=\"fw-bold\">${d.nomor_surat||'-'}</div></div>
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Tanggal Surat</div><div class=\"fw-bold\">${(d.tanggal_surat||'').substring(0,10)}</div></div>
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Tujuan</div><div class=\"fw-bold\">${d.tujuan||'-'}</div></div>
                <div class=\"col-12\"><div class=\"text-muted fs-8\">Perihal</div><div class=\"fw-bold\">${d.perihal||'-'}</div></div>
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Status</div><div class=\"fw-bold\">${badgeKeluar(d.status)}</div></div>
                <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Dibuat oleh</div><div class=\"fw-bold\">${d.created_by_name||'-'}</div></div>
            </div></div></div>`;
        const f=d.flow||{};
        const timeline = `
            <div class=\"timeline\">
                <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-file-text\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Draft Surat</div><div class=\"text-muted fs-8\">Dibuat oleh: ${d.created_by_name||'-'} | Tanggal: ${(d.created_at||'').substring(0,10)}</div></div></div>
                <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-person-check\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Persetujuan <span class=\"ms-2 badge ${f.approval?.status==='completed'?'badge-light-success':(f.approval?.status==='rejected'?'badge-light-danger':'badge-light-warning')}\">${f.approval?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Disetujui oleh: ${d.approved_by_name||'-'} | Tanggal: ${f.approval?.approved_at?String(f.approval.approved_at).substring(0,10):'-'}</div></div></div>
                <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-send\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Pengiriman <span class=\"ms-2 badge ${f.send?.status==='completed'?'badge-light-primary':'badge-light-warning'}\">${f.send?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Tanggal kirim: ${f.send?.tanggal_kirim?String(f.send.tanggal_kirim).substring(0,10):'-'}</div></div></div>
                <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-archive\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Arsip <span class=\"ms-2 badge ${f.arsip?.status==='completed'?'badge-light-success':'badge-light-warning'}\">${f.arsip?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Tanggal arsip: ${f.arsip?.archived_at?String(f.arsip.archived_at).substring(0,10):'-'}</div></div></div>
            </div>`;
        const lamp = (d.lampiran||[]).map(l=>`<a class=\"d-block\" href=\"${l.url}\" target=\"_blank\">${l.name}</a>`).join('');
        body.innerHTML = info + `<div class=\"card\"><div class=\"card-body\">${timeline}<div class=\"mt-5\"><div class=\"fw-bold mb-2\">Lampiran</div>${lamp||'-'}</div></div></div>`;
    }

    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const jenis = btn.getAttribute('data-jenis');
            if (body) body.innerHTML = '<div class="text-center py-10">Loading...</div>';
            const endpoint = jenis === 'masuk' ? `{{ url('/surat-masuk') }}/${id}` : `{{ url('/surat-keluar') }}/${id}`;
            fetch(endpoint)
                .then(r => r.json())
                .then(data => {
                    if (jenis === 'masuk') renderMasuk(data); else renderKeluar(data);
                })
                .catch(()=>{ body.innerHTML = '<div class="text-center text-danger py-10">Gagal memuat detail surat</div>'; });
        });
    });
})();
</script>
@endpush
