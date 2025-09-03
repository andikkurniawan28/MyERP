@extends('template.master')

@section('purchaseReport-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Laporan Pembelian</strong></h1>

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
                <label>Supplier</label>
                <select class="form-control select2" name="contact_id" data-placeholder="-- Semua --">
                    <option value="0">-- Semua --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Cabang</label>
                <select class="form-control select2" name="branch_id" data-placeholder="-- Semua --">
                    <option value="0">-- Semua --</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
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
                    <table id="purchaseReportTable" class="table table-bordered table-hover table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Faktur</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Tagihan</th>
                                <th>Dibayar</th>
                                <th>Sisa</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseReportBody">
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
                    url: "{{ route('report.purchaseReportData.data') }}",
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
                        $('#purchaseReportBody').html(rows);

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
