@extends('template.master')

@section('item_categories-active', 'active')

@section('content')
<div class="container-fluid py-0 px-0">
    <h1 class="h3 mb-3"><strong>Daftar Kategori Barang</strong></h1>

    @if(Auth()->user()->role->akses_tambah_kategori_barang)
    <div class="d-flex justify-content-between align-roles-center mb-3">
        <a href="{{ route('item_categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="roleTable" class="table table-bordered table-hover table-striped table-sm w-100 text-center">
                    <thead>
                        <tr>
                            {{-- <th>ID</th> --}}
                            <th>Nama</th>
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
        $('#roleTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('item_categories.index') }}",
            order: [[0, 'desc']],
            columns: [
                // { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
