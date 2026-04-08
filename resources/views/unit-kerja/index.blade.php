@extends('layouts.master')

@section('page_title', 'Unit Kerja')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Unit Kerja</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#unitKerjaModal" id="btnAddUnitKerja">
            <i class="bi bi-plus-lg me-2"></i>Tambah Unit
        </button>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="confirmDeleteUnitKerjaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteUnitKerjaForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Unit Kerja</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus unit kerja <strong id="delete_unit_kerja_name">-</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="unit_kerja_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Nama Unit</th>
                            <th class="text-end w-150px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($items as $it)
                            <tr>
                                <td>{{ $it->name }}</td>
                                <td class="text-end">
                                    <button class="btn btn-light-primary btn-sm me-2 btn-edit" data-id="{{ $it->id }}" data-bs-toggle="modal" data-bs-target="#unitKerjaModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-light-danger btn-sm btn-delete" data-id="{{ $it->id }}" data-name="{{ $it->name }}" data-bs-toggle="modal" data-bs-target="#confirmDeleteUnitKerjaModal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="unitKerjaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="unitKerjaForm" method="POST" action="{{ url('/admin/unit-kerja') }}">
                    @csrf
                    <input type="hidden" name="_method" id="unitKerjaFormMethod" value="POST">
                    <input type="hidden" name="id" id="unit_kerja_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="unitKerjaModalTitle">Tambah Unit Kerja</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label">Nama Unit</label>
                            <input type="text" class="form-control" name="name" id="unit_kerja_name" required maxlength="190">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveUnitKerja">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#unit_kerja_table').DataTable({
                pageLength: 10,
                ordering: true,
            });
        });

        const btnAdd = document.getElementById('btnAddUnitKerja');
        const form = document.getElementById('unitKerjaForm');
        const formMethod = document.getElementById('unitKerjaFormMethod');
        const inputId = document.getElementById('unit_kerja_id');
        const inputName = document.getElementById('unit_kerja_name');
        const title = document.getElementById('unitKerjaModalTitle');

        btnAdd?.addEventListener('click', () => {
            form.action = '{{ url('/admin/unit-kerja') }}';
            formMethod.value = 'POST';
            inputId.value = '';
            inputName.value = '';
            title.textContent = 'Tambah Unit Kerja';
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                form.action = '{{ url('/admin/unit-kerja') }}/' + id;
                formMethod.value = 'PUT';
                fetch(`{{ url('/admin/unit-kerja') }}/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        inputId.value = data.id;
                        inputName.value = data.name || '';
                        title.textContent = 'Edit Unit Kerja';
                    });
            });
        });

        // Delete handling
        const deleteForm = document.getElementById('deleteUnitKerjaForm');
        const deleteName = document.getElementById('delete_unit_kerja_name');
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                deleteForm.action = '{{ url('/admin/unit-kerja') }}/' + id;
                deleteName.textContent = name || '-';
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
