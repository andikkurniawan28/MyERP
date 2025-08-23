@extends('template.master')

@section('orders-active', 'active')

@section('content')
<div class="container-fluid py-0 px-0">
    <h1 class="h3 mb-3"><strong>Daftar Pesanan</strong></h1>

    @if(Auth()->user()->role->akses_tambah_pesanan ?? true)
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="ordersTable" class="table table-bordered table-striped table-sm w-100 text-center">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            {{-- <th>Jenis</th> --}}
                            <th>Kontak</th>
                            <th>Total</th>
                            {{-- <th>Status</th> --}}
                            <th>User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function() {
    $('#ordersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('orders.index') }}",
        order: [[0, 'desc']],
        columns: [
            { data: 'code', name: 'code' },
            { data: 'date', name: 'date' },
            // { data: 'type', name: 'type' },
            { data: 'contact', name: 'contact.name' },
            { data: 'grand_total', name: 'grand_total', className: 'text-end' },
            // { data: 'status', name: 'status' },
            { data: 'user', name: 'user.name' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endsection
