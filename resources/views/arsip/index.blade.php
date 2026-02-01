@extends('layouts.master')

@section('page_title', 'Arsip (Global)')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Arsip (Global)</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="arsip_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Jenis Surat</th>
                            <th>Nomor Surat</th>
                            <th>Perihal</th>
                            <th>Tanggal Surat</th>
                            <th>Diarsipkan Pada</th>
                            <th class="text-end w-200px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($arsip as $a)
                            <tr>
                                <td class="text-capitalize">{{ $a->jenis_surat }}</td>
                                <td>{{ $a->nomor_surat ?? '-' }}</td>
                                <td>{{ $a->perihal ?? '-' }}</td>
                                <td>{{ $a->tanggal_surat ? \Carbon\Carbon::parse($a->tanggal_surat)->format('Y-m-d') : '-' }}</td>
                                <td>{{ $a->archived_at ? \Carbon\Carbon::parse($a->archived_at)->format('Y-m-d H:i') : '-' }}</td>
                                <td class="text-end">
                                    @can('archive.read')
                                    <button class="btn btn-light-info btn-sm me-2 btn-view" data-id="{{ $a->id }}" data-bs-toggle="modal" data-bs-target="#detailModal">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Modal (auto menyesuaikan surat masuk/keluar) -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Arsip</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body" id="archive_detail_body" style="max-height:70vh;overflow-y:auto;"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @can('archive.manage')
    <!-- Restore Confirm Modal -->
    <div class="modal fade" id="confirmRestoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="restoreForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Restore Arsip</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin me-restore arsip ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Restore</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Arsip</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus arsip ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#arsip_table').DataTable({
                pageLength: 10,
                ordering: true,
            });
        });

        // View Detail: auto-render sesuai jenis_surat menggunakan endpoint show masing-masing
        (function(){
            const body = document.getElementById('archive_detail_body');
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
                    <div class="card mb-5"><div class="card-body"><div class="row g-3">
                        <div class="col-sm-6"><div class="text-muted fs-8">Nomor Surat</div><div class="fw-bold">${d.nomor_surat||'-'}</div></div>
                        <div class="col-sm-6"><div class="text-muted fs-8">Tanggal Terima</div><div class="fw-bold">${(d.tanggal_terima||'').substring(0,10)}</div></div>
                        <div class="col-sm-6"><div class="text-muted fs-8">Asal Surat</div><div class="fw-bold">${d.asal_surat||'-'}</div></div>
                        <div class="col-sm-6"><div class="text-muted fs-8">Pengirim</div><div class="fw-bold">${d.pengirim||'-'}</div></div>
                        <div class="col-12"><div class="text-muted fs-8">Perihal</div><div class="fw-bold">${d.perihal||'-'}</div></div>
                        <div class="col-sm-6"><div class="text-muted fs-8">Status</div><div class="fw-bold">${badgeMasuk(d.status)}</div></div>
                        <div class="col-sm-6"><div class="text-muted fs-8">Dibuat oleh</div><div class="fw-bold">${d.created_by_name||'-'}</div></div>
                    </div></div></div>`;
                const f=d.flow||{};
                const timeline = `
                    <div class="timeline">
                        <div class="timeline-item"><div class="timeline-line"></div><div class="timeline-icon bg-light"><i class="bi bi-inbox"></i></div><div class="timeline-content"><div class="fw-bold">Surat Diterima</div><div class="text-muted fs-8">Tanggal terima: ${(d.tanggal_terima||'').substring(0,10)} | Pengirim: ${d.pengirim||'-'}</div></div></div>
                        <div class="timeline-item"><div class="timeline-line"></div><div class="timeline-icon bg-light"><i class="bi bi-check-circle"></i></div><div class="timeline-content"><div class="fw-bold">Verifikasi <span class="ms-2 badge ${f.verifikasi?.status==='completed'?'badge-light-success':'badge-light-warning'}">${f.verifikasi?.status||'pending'}</span></div><div class="text-muted fs-8">Tanggal: ${f.verifikasi?.verified_at?String(f.verifikasi.verified_at).substring(0,10):'-'}</div></div></div>
                        <div class="timeline-item"><div class="timeline-line"></div><div class="timeline-icon bg-light"><i class="bi bi-share"></i></div><div class="timeline-content"><div class="fw-bold">Disposisi <span class="ms-2 badge ${f.disposisi?.status==='completed'?'badge-light-primary':'badge-light-warning'}">${f.disposisi?.status||'pending'}</span></div><div class="text-muted fs-8">Tanggal: ${f.disposisi?.tanggal?String(f.disposisi.tanggal).substring(0,10):'-'} | Catatan: ${f.disposisi?.catatan||'-'}</div></div></div>
                        <div class="timeline-item"><div class="timeline-line"></div><div class="timeline-icon bg-light"><i class="bi bi-clipboard-check"></i></div><div class="timeline-content"><div class="fw-bold">Tindak Lanjut <span class="ms-2 badge ${f.tindak_lanjut?.status==='completed'?'badge-light-success':'badge-light-warning'}">${f.tindak_lanjut?.status||'pending'}</span></div><div class="text-muted fs-8">Tanggal: ${f.tindak_lanjut?.tanggal?String(f.tindak_lanjut.tanggal).substring(0,10):'-'} | Deskripsi: ${f.tindak_lanjut?.deskripsi||'-'}</div></div></div>
                        <div class="timeline-item"><div class="timeline-line"></div><div class="timeline-icon bg-light"><i class="bi bi-archive"></i></div><div class="timeline-content"><div class="fw-bold">Arsip <span class="ms-2 badge ${f.arsip?.status==='completed'?'badge-light-success':'badge-light-warning'}">${f.arsip?.status||'pending'}</span></div><div class="text-muted fs-8">Tanggal arsip: ${f.arsip?.archived_at?String(f.arsip.archived_at).substring(0,10):'-'}</div></div></div>
                    </div>`;
                const lampSurat = (d.lampiran_surat||[]).map(l=>`<a class="d-block" href="${l.url}" target="_blank">${l.name}</a>`).join('');
                const lampTL = (d.lampiran_tindak_lanjut||[]).map(l=>`<a class="d-block" href="${l.url}" target="_blank">${l.name}</a>`).join('');
                body.innerHTML = info + `<div class="card"><div class="card-body">${timeline}
                    <div class="mt-5"><div class="fw-bold mb-2">Lampiran Surat</div>${lampSurat||'-'}</div>
                    <div class="mt-5"><div class="fw-bold mb-2">Lampiran Tindak Lanjut</div>${lampTL||'-'}</div>
                </div></div>`;
            }
            function renderKeluar(d){
                const info = `
                    <div class="card mb-5"><div class="card-body"><div class="row g-3">
                        <div class="col-sm-6"><div class="text-muted fs-8">Nomor Surat</div><div class="fw-bold">${d.nomor_surat||'-'}</div></div>
                        <div class="col-sm-6"><div class="text-muted fs-8">Tanggal Surat</div><div class="fw-bold">${(d.tanggal_surat||'').substring(0,10)}</div></div>
                        <div class="col-sm-6"><div class="text-muted fs-8">Tujuan</div><div class="fw-bold">${d.tujuan||'-'}</div></div>
                        <div class="col-12"><div class="text-muted fs-8">Perihal</div><div class="fw-bold">${d.perihal||'-'}</div></div>
                        <div class="col-sm-6"><div class="text-muted fs-8">Status</div><div class="fw-bold">${badgeKeluar(d.status)}</div></div>
                        <div class="col-sm-6"><div class="text-muted fs-8">Dibuat oleh</div><div class="fw-bold">${d.created_by_name||'-'}</div></div>
                    </div></div></div>`;
                const f=d.flow||{};
                const timeline = `
                    <div class="timeline">
                        <div class="timeline-item"><div class="timeline-line"></div><div class="timeline-icon bg-light"><i class="bi bi-file-text"></i></div><div class="timeline-content"><div class="fw-bold">Draft Surat</div><div class="text-muted fs-8">Dibuat oleh: ${d.created_by_name||'-'} | Tanggal: ${(d.created_at||'').substring(0,10)}</div></div></div>
                        <div class="timeline-item"><div class="timeline-line"></div><div class="timeline-icon bg-light"><i class="bi bi-person-check"></i></div><div class="timeline-content"><div class="fw-bold">Persetujuan <span class="ms-2 badge ${f.approval?.status==='completed'?'badge-light-success':(f.approval?.status==='rejected'?'badge-light-danger':'badge-light-warning')}">${f.approval?.status||'pending'}</span></div><div class="text-muted fs-8">Disetujui oleh: ${d.approved_by_name||'-'} | Tanggal: ${f.approval?.approved_at?String(f.approval.approved_at).substring(0,10):'-'}</div></div></div>
                        <div class="timeline-item"><div class="timeline-line"></div><div class="timeline-icon bg-light"><i class="bi bi-send"></i></div><div class="timeline-content"><div class="fw-bold">Pengiriman <span class="ms-2 badge ${f.send?.status==='completed'?'badge-light-primary':'badge-light-warning'}">${f.send?.status||'pending'}</span></div><div class="text-muted fs-8">Tanggal kirim: ${f.send?.tanggal_kirim?String(f.send.tanggal_kirim).substring(0,10):'-'}</div></div></div>
                        <div class="timeline-item"><div class="timeline-line"></div><div class="timeline-icon bg-light"><i class="bi bi-archive"></i></div><div class="timeline-content"><div class="fw-bold">Arsip <span class="ms-2 badge ${f.arsip?.status==='completed'?'badge-light-success':'badge-light-warning'}">${f.arsip?.status||'pending'}</span></div><div class="text-muted fs-8">Tanggal arsip: ${f.arsip?.archived_at?String(f.arsip.archived_at).substring(0,10):'-'}</div></div></div>
                    </div>`;
                const lamp = (d.lampiran||[]).map(l=>`<a class="d-block" href="${l.url}" target="_blank">${l.name}</a>`).join('');
                body.innerHTML = info + `<div class="card"><div class="card-body">${timeline}<div class="mt-5"><div class="fw-bold mb-2">Lampiran</div>${lamp||'-'}</div></div></div>`;
            }

            document.querySelectorAll('.btn-view').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    if (body) body.innerHTML = '<div class="text-center py-10">Loading...</div>';
                    fetch(`{{ url('/archive') }}/${id}`)
                        .then(r => r.json())
                        .then(meta => {
                            const jenis = (meta.jenis_surat||'').toLowerCase();
                            const suratId = meta.surat_id;
                            if (!suratId) { body.innerHTML = '<div class="text-center text-danger py-10">Data tidak lengkap</div>'; return; }
                            const endpoint = jenis === 'masuk' ? `{{ url('/surat-masuk') }}/${suratId}` : `{{ url('/surat-keluar') }}/${suratId}`;
                            fetch(endpoint)
                                .then(r => r.json())
                                .then(data => {
                                    if (jenis === 'masuk') renderMasuk(data); else renderKeluar(data);
                                })
                                .catch(()=>{ body.innerHTML = '<div class="text-center text-danger py-10">Gagal memuat detail surat</div>'; });
                        })
                        .catch(()=>{ body.innerHTML = '<div class="text-center text-danger py-10">Gagal memuat metadata arsip</div>'; });
                });
            });
        })();

        // Restore
        const restoreForm = document.getElementById('restoreForm');
        document.querySelectorAll('.btn-restore').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                restoreForm.action = `{{ url('/archive') }}/${id}/restore`;
            });
        });

        // Delete
        const deleteForm = document.getElementById('deleteForm');
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                deleteForm.action = `{{ url('/archive') }}/${id}`;
            });
        });
    </script>

    @if (session('success'))
        <script>
            (function() {
                var msg = @json(session('success'));
                if (window.toastr && toastr.success) {
                    toastr.success(msg);
                } else {
                    console.log('SUCCESS:', msg);
                }
            })();
        </script>
    @endif

    @if (session('error'))
        <script>
            (function() {
                var msg = @json(session('error'));
                if (window.toastr && toastr.error) {
                    toastr.error(msg);
                } else {
                    console.error('ERROR:', msg);
                }
            })();
        </script>
    @endif

    @if ($errors && $errors->any())
        <script>
            (function() {
                var errs = @json($errors->all());
                var msg = errs.join('\n');
                if (window.toastr && toastr.error) {
                    toastr.error(msg);
                } else {
                    console.error('ERRORS:', msg);
                }
            })();
        </script>
    @endif
@endpush
