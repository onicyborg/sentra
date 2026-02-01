@extends('layouts.master')

@section('page_title', 'Surat Keluar')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Surat Keluar</h3>
        @can('surat_keluar.create')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSuratKeluarModal" id="btnAddSuratKeluar">
            <i class="bi bi-plus-lg me-2"></i>Tambah Surat Keluar
        </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="surat_keluar_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Nomor Surat</th>
                            <th>Tanggal Surat</th>
                            <th>Tujuan</th>
                            <th>Perihal</th>
                            <th>Status</th>
                            <th class="text-end w-250px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($items as $it)
                            <tr>
                                <td>{{ $it->nomor_surat }}</td>
                                <td>{{ $it->tanggal_surat }}</td>
                                <td>{{ $it->tujuan }}</td>
                                <td>{{ $it->perihal }}</td>
                                <td>
                                    @php $status = strtolower($it->status); @endphp
                                    @if($status === 'draft')
                                        <span class="badge badge-light-secondary">Draft</span>
                                    @elseif($status === 'disahkan')
                                        <span class="badge badge-light-success">Disahkan</span>
                                    @elseif($status === 'terkirim')
                                        <span class="badge badge-light-primary">Terkirim</span>
                                    @elseif($status === 'ditolak')
                                        <span class="badge badge-light-danger">Ditolak</span>
                                    @else
                                        <span class="badge badge-light">{{ $it->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-light btn-sm me-2 btn-detail-sk" title="Lihat Detail" data-id="{{ $it->id }}" data-bs-toggle="modal" data-bs-target="#detailSuratKeluarModal">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @can('surat_keluar.create')
                                    @php $editable = in_array(strtolower($it->status), ['draft', 'ditolak']); @endphp
                                    <button class="btn btn-light-primary btn-sm me-2 btn-edit {{ $editable ? '' : 'disabled' }}" data-id="{{ $it->id }}" data-bs-toggle="modal" data-bs-target="#editSuratKeluarModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    @endcan
                                    @can('surat_keluar.approve')
                                    @php $approvable = strtolower($it->status) === 'draft'; @endphp
                                    <button class="btn btn-light-success btn-sm me-2 btn-approve {{ $approvable ? '' : 'disabled' }}" data-id="{{ $it->id }}" data-bs-toggle="modal" data-bs-target="#approveSuratKeluarModal">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                    @endcan
                                    @can('surat_keluar.send')
                                    @php $sendable = strtolower($it->status) === 'disahkan'; @endphp
                                    <button class="btn btn-light-success btn-sm btn-send {{ $sendable ? '' : 'disabled' }}" data-id="{{ $it->id }}" data-bs-toggle="modal" data-bs-target="#sendSuratKeluarModal">
                                        <i class="bi bi-send"></i>
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

    @include('surat-keluar.modal-create')
    @include('surat-keluar.modal-edit')
    @include('surat-keluar.modal-approve')
    @include('surat-keluar.modal-send')
    @include('surat-keluar.modal-detail')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#surat_keluar_table').DataTable({
                pageLength: 10,
                ordering: true,
                order: [[1, 'desc']],
            });
        });

        // Detail modal open & render
        (function(){
            const body = document.getElementById('sk_detail_body');
            function badgeSK(status){
                const s=(status||'').toLowerCase();
                if (s==='draft') return '<span class="badge badge-light-secondary">Draft</span>';
                if (s==='disahkan') return '<span class="badge badge-light-success">Disahkan</span>';
                if (s==='terkirim') return '<span class="badge badge-light-primary">Terkirim</span>';
                if (s==='ditolak') return '<span class="badge badge-light-danger">Ditolak</span>';
                return `<span class=\"badge badge-light\">${status||'-'}</span>`;
            }
            function renderDetailSK(d){
                const info = `
                    <div class=\"card mb-5\"><div class=\"card-body\"><div class=\"row g-3\">
                        <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Nomor Surat</div><div class=\"fw-bold\">${d.nomor_surat||'-'}</div></div>
                        <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Tanggal Surat</div><div class=\"fw-bold\">${(d.tanggal_surat||'').substring(0,10)}</div></div>
                        <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Tujuan</div><div class=\"fw-bold\">${d.tujuan||'-'}</div></div>
                        <div class=\"col-12\"><div class=\"text-muted fs-8\">Perihal</div><div class=\"fw-bold\">${d.perihal||'-'}</div></div>
                        <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Status</div><div class=\"fw-bold\">${badgeSK(d.status)}</div></div>
                        <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Dibuat oleh</div><div class=\"fw-bold\">${d.created_by_name||'-'}</div></div>
                        <div class=\"col-sm-6\"><div class=\"text-muted fs-8\">Tanggal dibuat</div><div class=\"fw-bold\">${(d.created_at||'').substring(0,10)}</div></div>
                    </div></div></div>`;
                const f = d.flow||{};
                const timeline = `
                    <div class=\"timeline\">
                        <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-file-text\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Draft Surat</div><div class=\"text-muted fs-8\">Dibuat oleh: ${d.created_by_name||'-'} | Tanggal: ${(d.created_at||'').substring(0,10)}</div></div></div>
                        <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-person-check\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Persetujuan <span class=\"ms-2 badge ${f.approval?.status==='completed'?'badge-light-success':(f.approval?.status==='rejected'?'badge-light-danger':'badge-light-warning')}\">${f.approval?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Disetujui oleh: ${d.approved_by_name||'-'} | Tanggal: ${f.approval?.approved_at?String(f.approval.approved_at).substring(0,10):'-'}</div></div></div>
                        <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-send\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Pengiriman <span class=\"ms-2 badge ${f.send?.status==='completed'?'badge-light-primary':'badge-light-warning'}\">${f.send?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Tanggal kirim: ${f.send?.tanggal_kirim?String(f.send.tanggal_kirim).substring(0,10):'-'}</div></div></div>
                        <div class=\"timeline-item\"><div class=\"timeline-line\"></div><div class=\"timeline-icon bg-light\"><i class=\"bi bi-archive\"></i></div><div class=\"timeline-content\"><div class=\"fw-bold\">Arsip <span class=\"ms-2 badge ${f.arsip?.status==='completed'?'badge-light-success':'badge-light-warning'}\">${f.arsip?.status||'pending'}</span></div><div class=\"text-muted fs-8\">Tanggal arsip: ${f.arsip?.archived_at?String(f.arsip.archived_at).substring(0,10):'-'}</div></div></div>
                    </div>`;
                const lamp = (d.lampiran||[]).map(l=>`<a class=\"d-block\" href=\"${l.url}\" target=\"_blank\">${l.name}</a>`).join('');
                if (body) body.innerHTML = info + `<div class=\"card\"><div class=\"card-body\">${timeline}<div class=\"mt-5\"><div class=\"fw-bold mb-2\">Lampiran</div>${lamp||'-'}</div></div></div>`;
            }
            document.querySelectorAll('.btn-detail-sk').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    if (body) body.innerHTML = '<div class="text-center py-10">Loading...</div>';
                    fetch(`{{ url('/surat-keluar') }}/${id}`)
                        .then(r => r.json())
                        .then(d => renderDetailSK(d))
                        .catch(()=>{ if (body) body.innerHTML = '<div class="text-center text-danger py-10">Gagal memuat detail</div>'; });
                });
            });
        })();

        // Create submit via AJAX
        const createForm = document.getElementById('createSuratKeluarForm');
        (function() {
            const dz = document.getElementById('c_dropzone_sk');
            const fileInput = document.getElementById('c_lampiran_sk');
            const list = document.getElementById('c_lampiran_list_sk');
            if (!dz || !fileInput || !list) return;

            let dt = new DataTransfer();

            function renderList() {
                list.innerHTML = '';
                if (dt.files.length === 0) return;
                for (let i = 0; i < dt.files.length; i++) {
                    const f = dt.files[i];
                    const row = document.createElement('div');
                    row.className = 'd-flex align-items-center justify-content-between border rounded p-2 mb-2';
                    const name = document.createElement('div');
                    name.textContent = `${f.name} (${Math.ceil(f.size/1024)} KB)`;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm btn-light-danger';
                    btn.textContent = 'Hapus';
                    btn.addEventListener('click', () => {
                        const ndt = new DataTransfer();
                        for (let j = 0; j < dt.files.length; j++) {
                            if (j !== i) ndt.items.add(dt.files[j]);
                        }
                        dt = ndt;
                        fileInput.files = dt.files;
                        renderList();
                    });
                    row.appendChild(name);
                    row.appendChild(btn);
                    list.appendChild(row);
                }
            }

            function addFiles(files) {
                for (const f of files) dt.items.add(f);
                fileInput.files = dt.files;
                renderList();
            }

            dz.addEventListener('click', () => { fileInput.value = ''; fileInput.click(); });
            dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('border-primary'); });
            dz.addEventListener('dragleave', () => dz.classList.remove('border-primary'));
            dz.addEventListener('drop', (e) => {
                e.preventDefault();
                dz.classList.remove('border-primary');
                if (e.dataTransfer?.files?.length) addFiles(e.dataTransfer.files);
            });
            fileInput.addEventListener('change', () => { if (fileInput.files?.length) addFiles(fileInput.files); });
        })();
        createForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(createForm);
            fetch('{{ route('surat-keluar.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            }).then(async r => {
                if (r.ok) { location.reload(); return; }
                const d = await r.json().catch(() => ({}));
                toastr.error(d.message || 'Gagal menyimpan surat keluar');
            });
        });

        // Edit
        const editForm = document.getElementById('editSuratKeluarForm');
        (function() {
            const dz = document.getElementById('e_dropzone_sk');
            const fileInput = document.getElementById('e_lampiran_sk');
            const list = document.getElementById('e_lampiran_new_list_sk');
            if (!dz || !fileInput || !list) return;

            let dt = new DataTransfer();
            function renderList() {
                list.innerHTML = '';
                if (dt.files.length === 0) return;
                for (let i = 0; i < dt.files.length; i++) {
                    const f = dt.files[i];
                    const row = document.createElement('div');
                    row.className = 'd-flex align-items-center justify-content-between border rounded p-2 mb-2';
                    const name = document.createElement('div');
                    name.textContent = `${f.name} (${Math.ceil(f.size/1024)} KB)`;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm btn-light-danger';
                    btn.textContent = 'Hapus';
                    btn.addEventListener('click', () => {
                        const ndt = new DataTransfer();
                        for (let j = 0; j < dt.files.length; j++) {
                            if (j !== i) ndt.items.add(dt.files[j]);
                        }
                        dt = ndt;
                        fileInput.files = dt.files;
                        renderList();
                    });
                    row.appendChild(name);
                    row.appendChild(btn);
                    list.appendChild(row);
                }
            }
            function addFiles(files) { for (const f of files) dt.items.add(f); fileInput.files = dt.files; renderList(); }
            function reset() { dt = new DataTransfer(); fileInput.files = dt.files; list.innerHTML = ''; }

            dz.addEventListener('click', () => { fileInput.value = ''; fileInput.click(); });
            dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('border-primary'); });
            dz.addEventListener('dragleave', () => dz.classList.remove('border-primary'));
            dz.addEventListener('drop', (e) => { e.preventDefault(); dz.classList.remove('border-primary'); if (e.dataTransfer?.files?.length) addFiles(e.dataTransfer.files); });
            fileInput.addEventListener('change', () => { if (fileInput.files?.length) addFiles(fileInput.files); });

            window._resetEditDropzoneSK = reset;
        })();
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                fetch(`{{ url('/surat-keluar') }}/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        if (typeof window._resetEditDropzoneSK === 'function') window._resetEditDropzoneSK();
                        const editable = !!data.editable;
                        const sendable = !!data.sendable;
                        const f = editForm;
                        f.action = `{{ url('/surat-keluar') }}/${id}`;
                        f.querySelector('#e_nomor_surat_sk').value = data.nomor_surat || '';
                        f.querySelector('#e_tanggal_surat_sk').value = (data.tanggal_surat || '').substring(0,10);
                        f.querySelector('#e_tujuan_sk').value = data.tujuan || '';
                        f.querySelector('#e_perihal_sk').value = data.perihal || '';

                        ['e_nomor_surat_sk','e_tanggal_surat_sk','e_tujuan_sk','e_perihal_sk','e_lampiran_sk'].forEach(idf => {
                            const el = f.querySelector('#'+idf);
                            if (el) el.disabled = !editable;
                        });
                        const uploadGroup = document.getElementById('e_lampiran_group_sk');
                        if (uploadGroup) uploadGroup.style.display = editable ? '' : 'none';

                        const list = document.getElementById('e_lampiran_list_sk');
                        list.innerHTML = '';
                        (data.lampiran || []).forEach(l => {
                            const a = document.createElement('a');
                            a.href = l.url; a.target = '_blank'; a.textContent = l.name; a.className = 'd-block mb-1';
                            list.appendChild(a);
                        });

                        const btnSend = document.getElementById('btnSendSuratKeluar');
                        if (btnSend) btnSend.disabled = !sendable;
                    });
            });
        });

        // Edit submit
        editForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = editForm.action;
            const formData = new FormData(editForm);
            formData.append('_method', 'PUT');
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            }).then(async r => {
                if (r.ok) { location.reload(); return; }
                const d = await r.json().catch(() => ({}));
                toastr.error(d.message || 'Gagal memperbarui surat keluar');
            });
        });

        // Send modal open & submit
        const sendForm = document.getElementById('sendSuratKeluarForm');
        document.querySelectorAll('.btn-send').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                if (sendForm) sendForm.action = `{{ url('/surat-keluar') }}/${id}/send`;
            });
        });
        sendForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = sendForm.action;
            const fd = new FormData(sendForm);
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: fd
            }).then(async r => {
                if (r.ok) { location.reload(); return; }
                const d = await r.json().catch(() => ({}));
                toastr.error(d.message || 'Gagal mengirim surat');
            });
        });

        // Approve modal open & submit
        const approveForm = document.getElementById('approveSuratKeluarForm');
        document.querySelectorAll('.btn-approve').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                if (approveForm) approveForm.action = `{{ url('/surat-keluar') }}/${id}/approve`;
            });
        });
        approveForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = approveForm.action;
            const fd = new FormData(approveForm);
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: fd
            }).then(async r => {
                if (r.ok) { location.reload(); return; }
                const d = await r.json().catch(() => ({}));
                toastr.error(d.message || 'Gagal memproses approval');
            });
        });
    </script>

    @if (session('success'))
        <script>
            (function() {
                var msg = @json(session('success'));
                if (window.toastr && toastr.success) { toastr.success(msg); } else { console.log('SUCCESS:', msg); }
            })();
        </script>
    @endif

    @if (session('error'))
        <script>
            (function() {
                var msg = @json(session('error'));
                if (window.toastr && toastr.error) { toastr.error(msg); } else { console.error('ERROR:', msg); }
            })();
        </script>
    @endif
@endpush
