@extends('layouts.master')

@section('page_title', 'Permissions')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Permissions</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#permissionModal" id="btnAddPermission">
            <i class="bi bi-plus-lg me-2"></i>Add Permission
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="permissions_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Permission Key</th>
                            <th>Description</th>
                            <th class="text-end w-150px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($permissions as $p)
                            <tr>
                                <td><code>{{ $p->permission_key }}</code></td>
                                <td>{{ $p->description ?: '-' }}</td>
                                <td class="text-end">
                                    <button class="btn btn-light-primary btn-sm me-2 btn-edit" data-id="{{ $p->id }}" data-bs-toggle="modal" data-bs-target="#permissionModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-light-danger btn-sm btn-delete" data-id="{{ $p->id }}" data-key="{{ $p->permission_key }}" data-bs-toggle="modal" data-bs-target="#confirmDeletePermissionModal">
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
    <div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="permissionForm" method="POST" action="{{ url('/admin/permissions') }}">
                    @csrf
                    <input type="hidden" name="_method" id="permissionFormMethod" value="POST">
                    <input type="hidden" name="id" id="permission_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="permissionModalTitle">Add Permission</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-5">
                                    <label class="form-label">Permission Key</label>
                                    <input type="text" class="form-control" name="permission_key" id="permission_key" required maxlength="190" placeholder="e.g. user.manage">
                                    <small class="text-muted d-block mt-1">Gunakan lowercase, angka, titik dan underscore saja.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-5">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" name="description" id="permission_description" maxlength="255">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSavePermission">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="confirmDeletePermissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deletePermissionForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Permission</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus permission <code id="delete_permission_key">-</code>?</p>
                        <p class="text-muted mb-0">Permission hanya dapat dihapus jika belum digunakan pada role.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#permissions_table').DataTable({
                pageLength: 10,
                ordering: true,
            });
        });

        const btnAdd = document.getElementById('btnAddPermission');
        const form = document.getElementById('permissionForm');
        const formMethod = document.getElementById('permissionFormMethod');
        const inputId = document.getElementById('permission_id');
        const inputKey = document.getElementById('permission_key');
        const inputDesc = document.getElementById('permission_description');
        const title = document.getElementById('permissionModalTitle');

        btnAdd?.addEventListener('click', () => {
            form.action = '{{ url('/admin/permissions') }}';
            formMethod.value = 'POST';
            inputId.value = '';
            inputKey.value = '';
            inputKey.readOnly = false;
            inputDesc.value = '';
            title.textContent = 'Add Permission';
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                form.action = '{{ url('/admin/permissions') }}/' + id;
                formMethod.value = 'PUT';
                fetch(`{{ url('/admin/permissions') }}/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        inputId.value = data.id;
                        inputKey.value = data.permission_key || '';
                        inputKey.readOnly = true; // read-only on edit
                        inputDesc.value = data.description || '';
                        title.textContent = 'Edit Permission';
                    });
            });
        });

        // Delete handling
        const deletePermissionForm = document.getElementById('deletePermissionForm');
        const deletePermissionKey = document.getElementById('delete_permission_key');
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const key = btn.getAttribute('data-key');
                deletePermissionForm.action = '{{ url('/admin/permissions') }}/' + id;
                deletePermissionKey.textContent = key || '-';
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
