@extends('layouts.master')

@section('page_title', 'Users')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Users</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" id="btnAddUser">
            <i class="bi bi-plus-lg me-2"></i>Add User
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="users_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th class="text-end w-150px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($users as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if($u->roles && $u->roles->count())
                                        @foreach ($u->roles as $r)
                                            <span class="badge badge-light-primary me-1 mb-1">{{ $r->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-light-primary btn-sm me-2 btn-edit" data-id="{{ $u->id }}" data-bs-toggle="modal" data-bs-target="#userModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-light-danger btn-sm btn-delete" data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
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
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="userForm" method="POST" action="{{ url('/admin/users') }}">
                    @csrf
                    <input type="hidden" name="_method" id="userFormMethod" value="POST">
                    <input type="hidden" name="id" id="user_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalTitle">Add User</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-5">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="user_name" required maxlength="150">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-5">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="user_email" required maxlength="150">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-5">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="user_password" minlength="6">
                                    <small class="text-muted d-block mt-1">Leave blank on edit to keep current password.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-5">
                                    <label class="form-label">Assign Role</label>
                                    <select class="form-select" name="role_id" id="user_role" style="width:100%">
                                        <option value="">- Select Role -</option>
                                        @foreach ($roles as $r)
                                            <option value="{{ $r->id }}">{{ $r->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveUser">Simpan</button>
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
                        <h5 class="modal-title">Hapus User</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus user <strong id="delete_name">-</strong>?</p>
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
            $('#users_table').DataTable({
                pageLength: 10,
                ordering: true,
            });
            $('#user_role').select2({
                dropdownParent: $('#userModal'),
                width: '100%'
            });
        });

        const btnAdd = document.getElementById('btnAddUser');
        const form = document.getElementById('userForm');
        const formMethod = document.getElementById('userFormMethod');
        const inputId = document.getElementById('user_id');
        const inputName = document.getElementById('user_name');
        const inputEmail = document.getElementById('user_email');
        const inputPassword = document.getElementById('user_password');
        const selectRole = document.getElementById('user_role');
        const title = document.getElementById('userModalTitle');

        btnAdd?.addEventListener('click', () => {
            form.action = '{{ url('/admin/users') }}';
            formMethod.value = 'POST';
            inputId.value = '';
            inputName.value = '';
            inputEmail.value = '';
            inputPassword.value = '';
            $(selectRole).val('').trigger('change');
            title.textContent = 'Add User';
            inputPassword.required = true;
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                form.action = '{{ url('/admin/users') }}/' + id;
                formMethod.value = 'PUT';
                fetch(`{{ url('/admin/users') }}/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        inputId.value = data.id;
                        inputName.value = data.name || '';
                        inputEmail.value = data.email || '';
                        const roleId = (data.roles && data.roles.length) ? data.roles[0].id : '';
                        $(selectRole).val(roleId).trigger('change');
                        title.textContent = 'Edit User';
                        inputPassword.value = '';
                        inputPassword.required = false;
                    });
            });
        });

        // Delete handling
        const deleteForm = document.getElementById('deleteForm');
        const deleteName = document.getElementById('delete_name');
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                deleteForm.action = '{{ url('/admin/users') }}/' + id;
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

