@extends('template.master')

@section('services-active', 'active')

@section('content')
<div class="container-fluid py-0 px-0">
    <h1 class="h3 mb-3"><strong>Daftar Jasa</strong></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('services.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="itemTable" class="table table-bordered table-hover table-striped table-sm w-100 text-center">
                    <thead>
                        <tr>
                            {{-- <th>ID</th> --}}
                            <th>Kode</th>
                            <th>Barcode</th>
                            <th>Nama</th>
                            {{-- <th>Satuan</th>
                            <th>Saldo</th> --}}
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
        $('#itemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('services.index') }}",
            order: [[0, 'asc']],
            columns: [
                // { data: 'id', name: 'id' },
                { data: 'code', name: 'code' },
                { data: 'barcode', name: 'barcode' },
                { data: 'name', name: 'name' },
                // { data: 'mainUnit', name: 'mainUnit.name' },
                // { data: 'saldo', name: 'saldo' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
