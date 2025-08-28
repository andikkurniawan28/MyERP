@extends('template.master')

@section('balanceSheet-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Neraca</strong></h1>

        {{-- Filter --}}
        <form id="filterForm" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Bulan</label>
                <input type="month" class="form-control" name="month" value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Terapkan</button>
            </div>
        </form>

        {{-- Tabel Neraca --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row" id="balanceSheetContainer">
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
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('report.balanceSheetData.data') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        let assetRows = '';
                        let liabilityRows = '';
                        let equityRows = '';

                        // asset
                        (res.balances.asset ?? []).forEach(acc => {
                            assetRows += `<tr>
                                <td>${acc.code}</td>
                                <td>${acc.name}</td>
                                <td class="text-end">${formatBalance(acc.balance)}</td>
                            </tr>`;
                        });
                        if (!assetRows) {
                            assetRows =
                                `<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>`;
                        }
                        assetRows += `<tr class="table-light fw-bold">
                            <td colspan="2">Total Asset</td>
                            <td class="text-end">${formatBalance(res.totals.asset ?? 0)}</td>
                        </tr>`;

                        // liability
                        (res.balances.liability ?? []).forEach(acc => {
                            liabilityRows += `<tr>
                                <td>${acc.code}</td>
                                <td>${acc.name}</td>
                                <td class="text-end">${formatBalance(acc.balance)}</td>
                            </tr>`;
                        });
                        if (!liabilityRows) {
                            liabilityRows =
                                `<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>`;
                        }
                        liabilityRows += `<tr class="table-light fw-bold">
                            <td colspan="2">Total Kewajiban</td>
                            <td class="text-end">${formatBalance(res.totals.liability ?? 0)}</td>
                        </tr>`;

                        // equity
                        (res.balances.equity ?? []).forEach(acc => {
                            equityRows += `<tr>
                                <td>${acc.code}</td>
                                <td>${acc.name}</td>
                                <td class="text-end">${formatBalance(acc.balance)}</td>
                            </tr>`;
                        });
                        if (!equityRows) {
                            equityRows =
                                `<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>`;
                        }
                        equityRows += `<tr class="table-light fw-bold">
                            <td colspan="2">Total Modal</td>
                            <td class="text-end">${formatBalance(res.totals.equity ?? 0)}</td>
                        </tr>`;

                        let html = `
                            <div class="col-md-6">
                                <h5 class="fw-bold">Asset</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kode</th>
                                                <th>Akun</th>
                                                <th class="text-end">Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>${assetRows}</tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold">Kewajiban</h5>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kode</th>
                                                <th>Akun</th>
                                                <th class="text-end">Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>${liabilityRows}</tbody>
                                    </table>
                                </div>

                                <h5 class="fw-bold">Modal</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kode</th>
                                                <th>Akun</th>
                                                <th class="text-end">Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>${equityRows}</tbody>
                                    </table>
                                </div>
                            </div>
                        `;

                        $('#balanceSheetContainer').html(html);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON);
                        alert("Terjadi kesalahan: " + xhr.statusText);
                    }
                });
            });

            // auto load pertama
            $('#filterForm').trigger('submit');
        });
    </script>
@endsection
