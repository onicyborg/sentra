@extends('layouts.master')

@section('page_title', 'Roles')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Roles</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal" id="btnAddRole">
            <i class="bi bi-plus-lg me-2"></i>Add Role
        </button>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="confirmDeleteRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteRoleForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Role</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus role <strong id="delete_role_name">-</strong>?</p>
                        <p class="text-muted mb-0">Role hanya dapat dihapus jika belum digunakan oleh user.</p>
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
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="roles_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Role Name</th>
                            <th>Description</th>
                            <th class="text-end w-150px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($roles as $r)
                            <tr>
                                <td>{{ $r->name }}</td>
                                <td>{{ $r->description ?: '-' }}</td>
                                <td class="text-end">
                                    <button class="btn btn-light-primary btn-sm me-2 btn-edit" data-id="{{ $r->id }}" data-bs-toggle="modal" data-bs-target="#roleModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-light-danger btn-sm btn-delete" data-id="{{ $r->id }}" data-name="{{ $r->name }}" data-bs-toggle="modal" data-bs-target="#confirmDeleteRoleModal">
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
    <div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="roleForm" method="POST" action="{{ url('/admin/roles') }}">
                    @csrf
                    <input type="hidden" name="_method" id="roleFormMethod" value="POST">
                    <input type="hidden" name="id" id="role_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="roleModalTitle">Add Role</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-5">
                                    <label class="form-label">Role Name</label>
                                    <input type="text" class="form-control" name="name" id="role_name" required maxlength="150">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-5">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" name="description" id="role_description" maxlength="255">
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Permissions</label>
                        </div>
                        <div class="scroll-y mh-400px pe-3">
                            <div class="row">
                                @foreach ($permissions as $p)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                            <div>
                                                <div class="fw-semibold">{{ $p->permission_key }}</div>
                                                <div class="text-muted fs-7">{{ $p->description }}</div>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" value="1" name="permissions[{{ $p->id }}]" id="perm_{{ $p->id }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveRole">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#roles_table').DataTable({
                pageLength: 10,
                ordering: true,
            });
        });

        const btnAdd = document.getElementById('btnAddRole');
        const form = document.getElementById('roleForm');
        const formMethod = document.getElementById('roleFormMethod');
        const inputId = document.getElementById('role_id');
        const inputName = document.getElementById('role_name');
        const inputDesc = document.getElementById('role_description');
        const title = document.getElementById('roleModalTitle');

        btnAdd?.addEventListener('click', () => {
            form.action = '{{ url('/admin/roles') }}';
            formMethod.value = 'POST';
            inputId.value = '';
            inputName.value = '';
            inputDesc.value = '';
            title.textContent = 'Add Role';
            // reset permissions
            document.querySelectorAll('#roleModal input[type=checkbox][name^="permissions"]').forEach(cb => cb.checked = false);
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                form.action = '{{ url('/admin/roles') }}/' + id;
                formMethod.value = 'PUT';
                fetch(`{{ url('/admin/roles') }}/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        inputId.value = data.id;
                        inputName.value = data.name || '';
                        inputDesc.value = data.description || '';
                        // set permission toggles
                        document.querySelectorAll('#roleModal input[type=checkbox][name^="permissions"]').forEach(cb => {
                            const pid = cb.name.match(/permissions\[(.+)\]/)[1];
                            cb.checked = data.permissions && data.permissions[pid] ? true : false;
                        });
                        title.textContent = 'Edit Role';
                    });
            });
        });

        // Delete handling
        const deleteRoleForm = document.getElementById('deleteRoleForm');
        const deleteRoleName = document.getElementById('delete_role_name');
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                deleteRoleForm.action = '{{ url('/admin/roles') }}/' + id;
                deleteRoleName.textContent = name || '-';
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
