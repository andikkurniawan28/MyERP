@extends('template.master')

@section('itemTransactionReport-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Laporan Mutasi Barang</strong></h1>

        {{-- Filter --}}
        <form id="filterForm" class="row g-3 mb-3">
            <div class="col-md-2">
                <label>Bulan</label>
                <input type="month" class="form-control" name="month"
                    value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
            </div>
            {{-- <div class="col-md-2">
                <label>Tipe</label>
                <select class="form-control select2" name="type" data-placeholder="-- Semua --">
                    <option value="0">-- Semua --</option>
                    <option value="purchase">Pembelian</option>
                    <option value="sales">Penjualan</option>
                    <option value="manual">Manual</option>
                </select>
            </div> --}}
            <div class="col-md-2">
                <label>Gudang</label>
                <select class="form-control select2" name="warehouse_id" data-placeholder="-- Semua --">
                    <option value="0">-- Semua --</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Admin</label>
                <select class="form-control select2" name="user_id" data-placeholder="-- Semua --">
                    <option value="0">-- Semua --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Barang</label>
                <select class="form-control select2" name="item_id" data-placeholder="-- Semua --" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Terapkan</button>
            </div>
        </form>

        {{-- Tabel Laporan --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="itemTransactionTable" class="table table-bordered table-hover table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Faktur</th>
                                <th>Tanggal</th>
                                <th>Gudang</th>
                                <th>Admin</th>
                                <th>Barang</th>
                                <th>Satuan</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="itemTransactionBody"></tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="6" class="text-end">TOTAL</th>
                                <th id="inSum" class="text-end">0</th>
                                <th id="outSum" class="text-end">0</th>
                                <th id="saldoSum" class="text-end">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('report.itemTransactionReportData.data') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        let rows = '';

                        // Baris saldo awal
                        rows += `
                            <tr class="table-warning fw-bold">
                                <td colspan="8" class="text-end">SALDO AWAL</td>
                                <td class="text-end">${new Intl.NumberFormat('id-ID').format(response.totals.saldo_awal)}</td>
                            </tr>`;

                        if (response.data.length === 0) {
                            rows +=
                                `<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>`;
                        } else {
                            response.data.forEach(function(tx) {
                                tx.details.forEach(function(detail) {
                                    rows += `
                                        <tr>
                                            <td>${tx.code}</td>
                                            <td>${tx.date}</td>
                                            <td>${tx.warehouse ? tx.warehouse.name : '-'}</td>
                                            <td>${tx.user ? tx.user.name : '-'}</td>
                                            <td>${detail.item ? detail.item.name : '-'}</td>
                                            <td>${detail.item && detail.item.main_unit ? detail.item.main_unit.name : '-'}</td>
                                            <td class="text-end">${detail.in ? new Intl.NumberFormat('id-ID').format(detail.in) : '-'}</td>
                                            <td class="text-end">${detail.out ? new Intl.NumberFormat('id-ID').format(detail.out) : '-'}</td>
                                            <td class="text-end">${new Intl.NumberFormat('id-ID').format(detail.saldo)}</td>
                                        </tr>`;
                                });
                            });
                        }

                        $('#itemTransactionBody').html(rows);

                        // update footer totals
                        $('#inSum').text(new Intl.NumberFormat('id-ID').format(response.totals
                            .in));
                        $('#outSum').text(new Intl.NumberFormat('id-ID').format(response.totals
                            .out));
                        $('#saldoSum').text(new Intl.NumberFormat('id-ID').format(response
                            .totals.saldo_akhir));
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan: " + xhr.statusText);
                    }
                });
            });
        });
    </script>
@endsection
