@extends('template.master')

@section('incomeStatement-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Laporan Laba Rugi</strong></h1>

        {{-- Filter --}}
        <form id="filterForm" class="row g-3 mb-3" method="POST" action="{{ route('report.closeIncomeStatement') }}">
            @csrf
            <div class="col-md-3">
                <label>Bulan</label>
                <input type="month" class="form-control" name="month" value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" name="action" value="apply" class="btn btn-primary w-100">Terapkan</button>
                <button type="submit" name="action" value="close" class="btn btn-success w-100">Tutupan</button>
            </div>
        </form>

        {{-- Tabel Laba Rugi --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row" id="incomeStatementContainer">
                    {{-- hasil ajax masuk sini --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function formatBalance(val) {
            if (val === 0 || val === null) return '-';
            if (val < 0) return `(${new Intl.NumberFormat('id-ID').format(Math.abs(val))})`;
            return new Intl.NumberFormat('id-ID').format(val);
        }

        $(document).ready(function() {
            let clickedAction = null;

            // simpan tombol mana yang diklik
            $('#filterForm button[type="submit"]').on('click', function() {
                clickedAction = $(this).val();
            });

            $('#filterForm').on('submit', function(e) {
                if (clickedAction === 'apply') {
                    e.preventDefault(); // stop submit normal
                    let formData = $(this).serialize();

                    $.ajax({
                        url: "{{ route('report.incomeStatementData.data') }}",
                        type: "POST",
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            let revenueRows = '';
                            let cogsRows = '';
                            let expenseRows = '';

                            // Pendapatan
                            (res.balances.revenue ?? []).forEach(acc => {
                                revenueRows += `<tr>
                                    <td>${acc.code}</td>
                                    <td>${acc.name}</td>
                                    <td class="text-end">${formatBalance(acc.balance)}</td>
                                </tr>`;
                            });
                            if (!revenueRows) {
                                revenueRows =
                                    `<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>`;
                            }
                            revenueRows += `<tr class="table-light fw-bold">
                                <td colspan="2">Total Pendapatan</td>
                                <td class="text-end">${formatBalance(res.totals.revenue ?? 0)}</td>
                            </tr>`;

                            // COGS
                            (res.balances.cogs ?? []).forEach(acc => {
                                cogsRows += `<tr>
                                    <td>${acc.code}</td>
                                    <td>${acc.name}</td>
                                    <td class="text-end">${formatBalance(acc.balance)}</td>
                                </tr>`;
                            });
                            if (!cogsRows) {
                                cogsRows =
                                    `<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>`;
                            }
                            cogsRows += `<tr class="table-light fw-bold">
                                <td colspan="2">Total HPP</td>
                                <td class="text-end">${formatBalance(res.totals.cogs ?? 0)}</td>
                            </tr>`;

                            // Beban
                            (res.balances.expense ?? []).forEach(acc => {
                                expenseRows += `<tr>
                                    <td>${acc.code}</td>
                                    <td>${acc.name}</td>
                                    <td class="text-end">${formatBalance(acc.balance)}</td>
                                </tr>`;
                            });
                            if (!expenseRows) {
                                expenseRows =
                                    `<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>`;
                            }
                            expenseRows += `<tr class="table-light fw-bold">
                                <td colspan="2">Total Beban</td>
                                <td class="text-end">${formatBalance(res.totals.expense ?? 0)}</td>
                            </tr>`;

                            // Laba Kotor & Bersih
                            let grossProfit = (res.totals.revenue ?? 0) - (res.totals.cogs ??
                            0);
                            let netProfit = grossProfit - (res.totals.expense ?? 0);

                            let summaryHtml = `
                                <div class="mt-3">
                                    <table class="table table-sm table-bordered">
                                        <tr class="fw-bold table-success">
                                            <td colspan="2">Laba Kotor</td>
                                            <td class="text-end">${formatBalance(grossProfit)}</td>
                                        </tr>
                                        <tr class="fw-bold ${netProfit >= 0 ? 'table-primary' : 'table-danger'}">
                                            <td colspan="2">${netProfit >= 0 ? 'Laba Bersih' : 'Rugi Bersih'}</td>
                                            <td class="text-end">${formatBalance(netProfit)}</td>
                                        </tr>
                                    </table>
                                </div>
                            `;

                            let html = `
                                <div class="col-md-12">
                                    <h5 class="fw-bold">Pendapatan</h5>
                                    <div class="table-responsive mb-5">
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Akun</th>
                                                    <th class="text-end">Saldo</th>
                                                </tr>
                                            </thead>
                                            <tbody>${revenueRows}</tbody>
                                        </table>
                                    </div>

                                    <h5 class="fw-bold">Harga Pokok Penjualan</h5>
                                    <div class="table-responsive mb-5">
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Akun</th>
                                                    <th class="text-end">Saldo</th>
                                                </tr>
                                            </thead>
                                            <tbody>${cogsRows}</tbody>
                                        </table>
                                    </div>

                                    <h5 class="fw-bold">Beban Operasional</h5>
                                    <div class="table-responsive mb-5">
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Akun</th>
                                                    <th class="text-end">Saldo</th>
                                                </tr>
                                            </thead>
                                            <tbody>${expenseRows}</tbody>
                                        </table>
                                    </div>

                                    ${summaryHtml}
                                </div>
                            `;

                            $('#incomeStatementContainer').html(html);
                        },
                        error: function(xhr) {
                            console.log(xhr.responseJSON);
                            alert("Terjadi kesalahan: " + xhr.statusText);
                        }
                    });
                }
                // kalau clickedAction == "close", biarkan form submit normal
            });

            // auto load pertama sebagai Terapkan
            clickedAction = 'apply';
            $('#filterForm').trigger('submit');
        });
    </script>
@endsection
