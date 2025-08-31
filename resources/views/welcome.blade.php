@extends('template.master')

@section('dashboard-active', 'active')

@section('content')
<div class="container-fluid py-0 px-0">

    <h1 class="h3 mb-4"><strong>Dashboard</strong></h1>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-3 col-6">
            <a href="{{ route('journals.create') }}" class="btn btn-primary w-100 shadow-sm mb-2">
                <i class="bi bi-journal-text"></i> Catat Jurnal Akuntansi
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('item_transactions.create') }}" class="btn btn-danger w-100 shadow-sm mb-2">
                <i class="bi bi-arrow-left-right"></i> Catat Mutasi Barang
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('purchases.create') }}" class="btn btn-success w-100 shadow-sm mb-2">
                <i class="bi bi-receipt"></i> Catat Pembelian
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('sales.create') }}" class="btn btn-dark w-100 shadow-sm mb-2">
                <i class="bi bi-receipt"></i> Catat Penjualan
            </a>
        </div>
    </div>

    {{-- Card Dashboard --}}
    <div class="row">
        <div class="col-xl-8 col-xxl-8 d-flex">
            <div class="w-100">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Penjualan Bulan Ini</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="bi bi-cart-check-fill align-middle"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3" id="total_sales">0</h1>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Piutang Usaha s/d Saat Ini</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="bi bi-cash-coin align-middle"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3" id="piutang_usaha">0</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Pembelian Bulan Ini</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="bi bi-bag-check-fill align-middle"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3" id="total_purchases">0</h1>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Hutang Usaha s/d Saat Ini</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="bi bi-cash-stack align-middle"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3" id="hutang_usaha">0</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 5 Produk Terlaris --}}
        <div class="col-xl-4 col-xxl-4 d-flex">
            <div class="card flex-fill w-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Produk Terlaris <i class="bi bi-tags align-middle"></i></h5>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>Produk</th>
                                    <th>Satuan</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="top-products">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart Penjualan Harian --}}
        <div class="col-12 mt-1">
            <div class="card flex-fill w-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Penjualan Harian ({{ now()->format('F Y') }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart" height="100"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    $.ajax({
        url: "{{ route('dashboard.data') }}",
        type: "GET",
        success: function(res) {
            // Update cards
            $('#total_sales').text(new Intl.NumberFormat('id-ID').format(res.total_sales ?? 0));
            $('#piutang_usaha').text(new Intl.NumberFormat('id-ID').format(res.piutang_usaha ?? 0));
            $('#total_purchases').text(new Intl.NumberFormat('id-ID').format(res.total_purchases ?? 0));
            $('#hutang_usaha').text(new Intl.NumberFormat('id-ID').format(res.hutang_usaha ?? 0));

            // Handle Top 5 Produk Terlaris
            let topProductsRows = '';
            if (res.topSellingProducts && res.topSellingProducts.length > 0) {
                res.topSellingProducts.forEach((item) => {
                    topProductsRows += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.unit_name}</td>
                        <td>${new Intl.NumberFormat('id-ID').format(item.total_qty ?? 0)}</td>
                    </tr>
                    `;
                });
            } else {
                topProductsRows = `
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                `;
            }
            $('#top-products').html(topProductsRows);

            // Chart Penjualan Harian
            const labels = Object.keys(res.dailySales);
            const data = Object.values(res.dailySales);

            const ctx = document.getElementById('dailySalesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Penjualan',
                        data: data,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: { title: { display: true, text: 'Tanggal' } },
                        y: {
                            title: { display: true, text: 'Jumlah Penjualan (Rp)' },
                            beginAtZero: true
                        }
                    }
                }
            });

        },
        error: function(xhr) {
            console.error(xhr.responseText);
        }
    });
});
</script>
@endsection
