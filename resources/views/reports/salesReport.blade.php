@extends('template.master')

@section('salesReport-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Laporan Penjualan</strong></h1>

        {{-- Filter --}}
        <form id="filterForm" class="row g-3 mb-3">
            <div class="col-md-2">
                <label>Bulan</label>
                <input type="month" class="form-control" name="month"
                    value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select class="form-control select2" name="status" data-placeholder="-- Semua --">
                    <option value="0">-- Semua --</option>
                    <option value="Lunas">Lunas</option>
                    <option value="Belum Tuntas">Belum Tuntas</option>
                    <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Customer</label>
                <select class="form-control select2" name="contact_id" data-placeholder="-- Semua --">
                    <option value="0">-- Semua --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
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
                <label>Gudang</label>
                <select class="form-control select2" name="warehouse_id" data-placeholder="-- Semua --">
                    <option value="0">-- Semua --</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
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
                    <table id="salesReportTable" class="table table-bordered table-hover table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Tagihan</th>
                                <th>Dibayar</th>
                                <th>Sisa</th>
                            </tr>
                        </thead>
                        <tbody id="salesReportBody">
                            {{-- default kosong --}}
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="3" class="text-end">TOTAL</th>
                                <th id="grandTotalSum" class="text-end">0</th>
                                <th id="paidSum" class="text-end">0</th>
                                <th id="remainingSum" class="text-end">0</th>
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

                $.ajax({
                    url: "{{ route('report.salesReportData.data') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        let rows = '';
                        if (response.data.length === 0) {
                            rows =
                                `<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>`;
                        } else {
                            response.data.forEach(function(item) {
                                rows += `
                            <tr>
                                <td>${item.code}</td>
                                <td>${item.date}</td>
                                <td>${item.contact ? item.contact.name : '-'}</td>
                                <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.grand_total)}</td>
                                <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.paid)}</td>
                                <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.remaining)}</td>
                            </tr>`;
                            });
                        }
                        $('#salesReportBody').html(rows);

                        // update footer totals
                        $('#grandTotalSum').text(new Intl.NumberFormat('id-ID').format(response
                            .totals.grand_total));
                        $('#paidSum').text(new Intl.NumberFormat('id-ID').format(response.totals
                            .paid));
                        $('#remainingSum').text(new Intl.NumberFormat('id-ID').format(response
                            .totals.remaining));
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan: " + xhr.statusText);
                    }
                });
            });
        });
    </script>
@endsection
