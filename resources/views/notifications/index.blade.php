@extends('layouts.master')

@section('page_title','Notifikasi')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Notifikasi</h3>
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-light-primary">Tandai Semua Terbaca</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="notif_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Title</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($items as $n)
                            <tr>
                                <td>{{ $n->title }}</td>
                                <td>{{ $n->message }}</td>
                                <td>
                                    @if ($n->is_read)
                                        <span class="badge badge-light-success">Read</span>
                                    @else
                                        <span class="badge badge-light-warning">Unread</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</td>
                                <td class="text-end">
                                    @if (!$n->is_read)
                                    <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light-primary">Mark as Read</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        $('#notif_table').DataTable({ pageLength: 10, order: [[3,'desc']] });
    });
</script>
@endpush
