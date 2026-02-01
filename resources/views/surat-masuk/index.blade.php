@extends('layouts.master')

@section('page_title', 'Surat Masuk')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Surat Masuk</h3>
        @can('surat_masuk.create')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSuratMasukModal" id="btnAddSuratMasuk">
            <i class="bi bi-plus-lg me-2"></i>Tambah Surat Masuk
        </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="surat_masuk_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Nomor Surat</th>
                            <th>Tanggal Terima</th>
                            <th>Asal Surat</th>
                            <th>Pengirim</th>
                            <th>Perihal</th>
                            <th>Status</th>
                            <th class="text-end w-250px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($items as $it)
                            <tr>
                                <td>{{ $it->nomor_surat }}</td>
                                <td>{{ $it->tanggal_terima }}</td>
                                <td>{{ $it->asal_surat ?: '-' }}</td>
                                <td>{{ $it->pengirim ?: '-' }}</td>
                                <td>{{ $it->perihal }}</td>
                                <td>
                                    @php $status = strtolower($it->status); @endphp
                                    @if($status === 'draft')
                                        <span class="badge badge-light-secondary">Draft</span>
                                    @elseif($status === 'diterima')
                                        <span class="badge badge-light-info">Diterima</span>
                                    @elseif($status === 'terverifikasi')
                                        <span class="badge badge-light-success">Terverifikasi</span>
                                    @elseif($status === 'didisposisikan')
                                        <span class="badge badge-light-primary">Didisposisikan</span>
                                    @else
                                        <span class="badge badge-light">{{ $it->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @can('surat_masuk.create')
                                    @php $editable = in_array(strtolower($it->status), ['draft','diterima']); @endphp
                                    <button class="btn btn-light-primary btn-sm me-2 btn-edit {{ $editable ? '' : 'disabled' }}" data-id="{{ $it->id }}" data-bs-toggle="modal" data-bs-target="#editSuratMasukModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    @endcan

                                    @can('surat_masuk.verify')
                                    @php $verifiable = strtolower($it->status) === 'diterima'; @endphp
                                    <button class="btn btn-light-success btn-sm me-2 btn-verify {{ $verifiable ? '' : 'disabled' }}" data-id="{{ $it->id }}" data-bs-toggle="modal" data-bs-target="#verifySuratMasukModal">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                    @endcan

                                    @can('surat_masuk.distribute')
                                    @php $distributable = strtolower($it->status) === 'terverifikasi'; @endphp
                                    <button class="btn btn-light-info btn-sm btn-disposisi {{ $distributable ? '' : 'disabled' }}" data-id="{{ $it->id }}" data-bs-toggle="modal" data-bs-target="#disposisiSuratMasukModal">
                                        <i class="bi bi-share"></i>
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

    @include('surat-masuk.modal-create')
    @include('surat-masuk.modal-edit')
    @include('surat-masuk.modal-verify')
    @include('surat-masuk.modal-disposisi')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#surat_masuk_table').DataTable({
                pageLength: 10,
                ordering: true,
                order: [[1, 'desc']], // sort by tanggal terima desc by default
            });
        });

        // Create submit via AJAX
        const createForm = document.getElementById('createSuratMasukForm');
        // Save/Submit buttons (create)
        (function() {
            const btnSave = document.getElementById('btnCreateSave');
            const btnSubmit = document.getElementById('btnCreateSubmit');
            const statusInput = document.getElementById('c_status');
            btnSave?.addEventListener('click', () => { if (statusInput) statusInput.value = 'draft'; createForm?.requestSubmit(); });
            btnSubmit?.addEventListener('click', () => { if (statusInput) statusInput.value = 'diterima'; createForm?.requestSubmit(); });
        })();
        // Dropzone wiring for create modal
        (function() {
            const dz = document.getElementById('c_dropzone');
            const fileInput = document.getElementById('c_lampiran');
            const list = document.getElementById('c_lampiran_list');
            if (!dz || !fileInput || !list) return;

            let dt = new DataTransfer();

            function renderList() {
                list.innerHTML = '';
                if (dt.files.length === 0) {
                    return;
                }
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
                for (const f of files) {
                    dt.items.add(f);
                }
                fileInput.files = dt.files;
                renderList();
            }

            dz.addEventListener('click', () => { fileInput.value = ''; fileInput.click(); });
            dz.addEventListener('dragover', (e) => {
                e.preventDefault();
                dz.classList.add('border-primary');
            });
            dz.addEventListener('dragleave', () => dz.classList.remove('border-primary'));
            dz.addEventListener('drop', (e) => {
                e.preventDefault();
                dz.classList.remove('border-primary');
                if (e.dataTransfer?.files?.length) addFiles(e.dataTransfer.files);
            });
            fileInput.addEventListener('change', () => {
                if (fileInput.files?.length) addFiles(fileInput.files);
            });
        })();
        createForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(createForm);
            fetch('{{ route('surat-masuk.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            }).then(async r => {
                if (r.ok) { location.reload(); return; }
                const d = await r.json().catch(() => ({}));
                toastr.error(d.message || 'Gagal menyimpan surat masuk');
            });
        });

        // Edit open & submit via AJAX
        const editForm = document.getElementById('editSuratMasukForm');
        // Save/Submit buttons (edit)
        (function() {
            const btnSave = document.getElementById('btnUpdateSave');
            const btnSubmit = document.getElementById('btnUpdateSubmit');
            const statusInput = document.getElementById('e_status');
            btnSave?.addEventListener('click', () => { if (statusInput) statusInput.value = 'draft'; editForm?.requestSubmit(); });
            btnSubmit?.addEventListener('click', () => { if (statusInput) statusInput.value = 'diterima'; editForm?.requestSubmit(); });
        })();
        // Dropzone wiring for edit modal
        (function() {
            const dz = document.getElementById('e_dropzone');
            const fileInput = document.getElementById('e_lampiran');
            const list = document.getElementById('e_lampiran_new_list');
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

            function reset() {
                dt = new DataTransfer();
                fileInput.files = dt.files;
                list.innerHTML = '';
            }

            dz.addEventListener('click', () => { fileInput.value = ''; fileInput.click(); });
            dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('border-primary'); });
            dz.addEventListener('dragleave', () => dz.classList.remove('border-primary'));
            dz.addEventListener('drop', (e) => {
                e.preventDefault();
                dz.classList.remove('border-primary');
                if (e.dataTransfer?.files?.length) addFiles(e.dataTransfer.files);
            });
            fileInput.addEventListener('change', () => {
                if (fileInput.files?.length) addFiles(fileInput.files);
            });

            // expose reset for when edit modal opens
            window._resetEditDropzone = reset;
        })();
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                fetch(`{{ url('/surat-masuk') }}/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        // Reset new upload list
                        if (typeof window._resetEditDropzone === 'function') window._resetEditDropzone();
                        const editable = !!data.editable;
                        const f = editForm;
                        f.action = `{{ url('/surat-masuk') }}/${id}`;
                        f.querySelector('#e_nomor_surat').value = data.nomor_surat || '';
                        f.querySelector('#e_tanggal_terima').value = (data.tanggal_terima || '').substring(0,10);
                        f.querySelector('#e_asal_surat').value = data.asal_surat || '';
                        f.querySelector('#e_pengirim').value = data.pengirim || '';
                        f.querySelector('#e_perihal').value = data.perihal || '';

                        // toggle readonly based on editable
                        ['e_nomor_surat','e_tanggal_terima','e_asal_surat','e_pengirim','e_perihal','e_lampiran'].forEach(idf => {
                            const el = f.querySelector('#'+idf);
                            if (el) el.disabled = !editable;
                        });

                        // list lampiran
                        const list = document.getElementById('e_lampiran_list');
                        list.innerHTML = '';
                        (data.lampiran || []).forEach(l => {
                            const a = document.createElement('a');
                            a.href = l.url;
                            a.target = '_blank';
                            a.textContent = l.name;
                            a.className = 'd-block mb-1';
                            list.appendChild(a);
                        });

                        // hide upload if not editable
                        const uploadGroup = document.getElementById('e_lampiran_group');
                        if (uploadGroup) uploadGroup.style.display = editable ? '' : 'none';

                        // set submit button state
                        document.getElementById('btnUpdateSuratMasuk').disabled = !editable;
                    });
            });
        });

        // Verify modal open & submit
        const verifyForm = document.getElementById('verifySuratMasukForm');
        document.querySelectorAll('.btn-verify').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                if (verifyForm) verifyForm.action = `{{ url('/surat-masuk') }}/${id}/verify`;
            });
        });
        verifyForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = verifyForm.action;
            const fd = new FormData(verifyForm);
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: fd
            }).then(async r => {
                if (r.ok) { location.reload(); return; }
                const d = await r.json().catch(() => ({}));
                toastr.error(d.message || 'Gagal memverifikasi surat');
            });
        });

        // Disposisi modal open & submit
        const disposisiForm = document.getElementById('disposisiSuratMasukForm');
        document.querySelectorAll('.btn-disposisi').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                if (disposisiForm) disposisiForm.action = `{{ url('/surat-masuk') }}/${id}/disposisi`;
            });
        });
        disposisiForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = disposisiForm.action;
            const fd = new FormData(disposisiForm);
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: fd
            }).then(async r => {
                if (r.ok) { location.reload(); return; }
                const d = await r.json().catch(() => ({}));
                toastr.error(d.message || 'Gagal mendisposisikan surat');
            });
        });

        editForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = editForm.action;
            const formData = new FormData(editForm);
            formData.append('_method', 'PUT');
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            }).then(async r => {
                if (r.ok) { location.reload(); return; }
                const d = await r.json().catch(() => ({}));
                toastr.error(d.message || 'Gagal memperbarui surat masuk');
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
@endpush
