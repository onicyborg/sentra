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

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Arsip</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted fs-7">Jenis Surat</div>
                                <div id="d_jenis" class="fw-semibold">-</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted fs-7">Nomor Surat</div>
                                <div id="d_nomor" class="fw-semibold">-</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted fs-7">Perihal</div>
                                <div id="d_perihal" class="fw-semibold">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted fs-7">Tanggal Surat</div>
                                <div id="d_tanggal" class="fw-semibold">-</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted fs-7">Diarsipkan Pada</div>
                                <div id="d_archived" class="fw-semibold">-</div>
                            </div>
                        </div>
                    </div>
                </div>
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

        // View Detail
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                fetch(`{{ url('/admin/arsip') }}/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('d_jenis').textContent = data.jenis_surat || '-';
                        document.getElementById('d_nomor').textContent = data.nomor_surat || '-';
                        document.getElementById('d_perihal').textContent = data.perihal || '-';
                        document.getElementById('d_tanggal').textContent = data.tanggal_surat || '-';
                        document.getElementById('d_archived').textContent = data.archived_at || '-';
                    });
            });
        });

        // Restore
        const restoreForm = document.getElementById('restoreForm');
        document.querySelectorAll('.btn-restore').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                restoreForm.action = `{{ url('/admin/arsip') }}/${id}/restore`;
            });
        });

        // Delete
        const deleteForm = document.getElementById('deleteForm');
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                deleteForm.action = `{{ url('/admin/arsip') }}/${id}`;
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
