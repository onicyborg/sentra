@extends('layouts.master')

@section('page_title', 'Activity Logs')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Activity Logs</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="logs_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Method</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th class="text-end w-125px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($logs as $l)
                            <tr>
                                <td>{{ $l->created_at }}</td>
                                <td>
                                    @php
                                        $uname = $l->user_name ?? null;
                                        $uemail = $l->user_email ?? null;
                                    @endphp
                                    {{ $uname ? $uname : '-' }}
                                    <div class="text-muted fs-8">{{ $uemail ? $uemail : '' }}</div>
                                </td>
                                <td><span class="badge badge-light-secondary">{{ $l->method }}</span></td>
                                <td>
                                    @php
                                        $short = strlen($l->url) > 60 ? substr($l->url, 0, 57).'…' : $l->url;
                                    @endphp
                                    <span title="{{ $l->url }}">{{ $short }}</span>
                                </td>
                                <td>{{ $l->status_code }}</td>
                                <td class="text-end">
                                    <button class="btn btn-light-primary btn-sm btn-detail" data-id="{{ $l->id }}" data-bs-toggle="modal" data-bs-target="#logDetailModal">
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

    <!-- Detail Modal -->
    <div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Log</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted fs-7">User</div>
                                <div id="d_user" class="fw-semibold">-</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted fs-7">Method</div>
                                <div id="d_method" class="fw-semibold">-</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted fs-7">Status Code</div>
                                <div id="d_status" class="fw-semibold">-</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted fs-7">IP Address</div>
                                <div id="d_ip" class="fw-semibold">-</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted fs-7">User Agent</div>
                                <div id="d_ua" class="fw-semibold text-break">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted fs-7">URL</div>
                                <div id="d_url" class="fw-semibold text-break">-</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted fs-7">Waktu</div>
                                <div id="d_time" class="fw-semibold">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted fs-7">Request Payload</div>
                                <pre id="d_req" class="bg-light p-3 rounded border small mb-0" style="white-space:pre-wrap; word-wrap:break-word;">-</pre>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-muted fs-7">Response Payload</div>
                                <pre id="d_res" class="bg-light p-3 rounded border small mb-0" style="white-space:pre-wrap; word-wrap:break-word;">-</pre>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#logs_table').DataTable({
                pageLength: 10,
                ordering: true,
                order: [[0, 'desc']],
            });
        });

        // Detail
        document.querySelectorAll('.btn-detail').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                fetch(`{{ url('/admin/activity-logs') }}/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        const name = data.user && data.user.name ? data.user.name : '-';
                        const email = data.user && data.user.email ? data.user.email : '';
                        document.getElementById('d_user').textContent = email ? `${name} (${email})` : name;
                        document.getElementById('d_method').textContent = data.method || '-';
                        document.getElementById('d_status').textContent = data.status_code || '-';
                        document.getElementById('d_ip').textContent = data.ip_address || '-';
                        document.getElementById('d_ua').textContent = data.user_agent || '-';
                        document.getElementById('d_url').textContent = data.url || '-';
                        document.getElementById('d_time').textContent = data.created_at || '-';

                        function pretty(obj) {
                            if (obj === null || obj === undefined) return '-';
                            try { return JSON.stringify(obj, null, 2); } catch(e) { return String(obj); }
                        }
                        document.getElementById('d_req').textContent = pretty(data.request_payload);
                        document.getElementById('d_res').textContent = pretty(data.response_payload);
                    });
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
