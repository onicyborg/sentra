@extends('layouts.master')

@section('page_title', 'Tindak Lanjut Surat Masuk')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Tindak Lanjut Surat Masuk</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="tindak_lanjut_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Nomor Surat</th>
                            <th>Tanggal Terima</th>
                            <th>Asal Surat</th>
                            <th>Perihal</th>
                            <th>Catatan Disposisi</th>
                            <th>Status</th>
                            <th class="text-end w-200px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($items as $it)
                            @php $status = strtolower($it->status); @endphp
                            <tr>
                                <td>{{ $it->nomor_surat }}</td>
                                <td>{{ $it->tanggal_terima }}</td>
                                <td>{{ $it->asal_surat ?: '-' }}</td>
                                <td>{{ $it->perihal }}</td>
                                <td>{{ optional($notes->get($it->id))->catatan ?: '-' }}</td>
                                <td>
                                    @if($status === 'didisposisikan')
                                        <span class="badge badge-light-primary">Didisposisikan</span>
                                    @elseif($status === 'ditindaklanjuti')
                                        <span class="badge badge-light-success">Ditindaklanjuti</span>
                                    @else
                                        <span class="badge badge-light">{{ $it->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @can('surat_masuk.follow_up')
                                    @php $canFollowUp = ($status === 'didisposisikan'); @endphp
                                    <button class="btn btn-light-primary btn-sm btn-followup {{ $canFollowUp ? '' : 'disabled' }}" data-id="{{ $it->id }}" data-bs-toggle="modal" data-bs-target="#createTindakLanjutModal">
                                        <i class="bi bi-journal-check me-1"></i>Tindak Lanjut
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

    @include('tindak-lanjut.modal-create')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tindak_lanjut_table').DataTable({
                pageLength: 10,
                ordering: true,
                order: [[1, 'desc']],
            });
        });

        // Open modal and set form action
        const createForm = document.getElementById('createTindakLanjutForm');
        document.querySelectorAll('.btn-followup').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                if (createForm) createForm.action = `{{ url('/tindak-lanjut') }}/${id}`;
                // reset form
                createForm?.reset();
                if (typeof window._resetTLDropzone === 'function') window._resetTLDropzone();
            });
        });

        // Submit via AJAX
        createForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = createForm.action;
            const fd = new FormData(createForm);
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: fd
            }).then(async r => {
                if (r.ok) { location.reload(); return; }
                const d = await r.json().catch(() => ({}));
                toastr.error(d.message || 'Gagal menyimpan tindak lanjut');
            });
        });

        // Simple dropzone like existing pattern
        (function() {
            const dz = document.getElementById('tl_dropzone');
            const fileInput = document.getElementById('tl_lampiran');
            const list = document.getElementById('tl_lampiran_list');
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
                    btn.type = 'button'; btn.className = 'btn btn-sm btn-light-danger'; btn.textContent = 'Hapus';
                    btn.addEventListener('click', () => {
                        const ndt = new DataTransfer();
                        for (let j = 0; j < dt.files.length; j++) if (j !== i) ndt.items.add(dt.files[j]);
                        dt = ndt; fileInput.files = dt.files; renderList();
                    });
                    row.appendChild(name); row.appendChild(btn); list.appendChild(row);
                }
            }
            function addFiles(files) { for (const f of files) dt.items.add(f); fileInput.files = dt.files; renderList(); }
            function reset() { dt = new DataTransfer(); fileInput.files = dt.files; list.innerHTML=''; }

            dz.addEventListener('click', () => { fileInput.value = ''; fileInput.click(); });
            dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('border-primary'); });
            dz.addEventListener('dragleave', () => dz.classList.remove('border-primary'));
            dz.addEventListener('drop', (e) => { e.preventDefault(); dz.classList.remove('border-primary'); if (e.dataTransfer?.files?.length) addFiles(e.dataTransfer.files); });
            fileInput.addEventListener('change', () => { if (fileInput.files?.length) addFiles(fileInput.files); });

            window._resetTLDropzone = reset;
        })();
    </script>
@endpush
