@extends('template.master')

@section('generalLedger-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Buku Besar</strong></h1>

        {{-- Filter --}}
        <form id="filterForm" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Akun</label>
                <select class="form-control select2" name="account_id" required>
                    <option value="">-- Pilih Akun --</option>
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Tanggal Awal</label>
                <input type="date" class="form-control" name="start_date" required
                    value="{{ \Carbon\Carbon::now()->startOfMonth()->toDateString() }}">
            </div>
            <div class="col-md-3">
                <label>Tanggal Akhir</label>
                <input type="date" class="form-control" name="end_date" required
                    value="{{ \Carbon\Carbon::now()->endOfMonth()->toDateString() }}">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Terapkan</button>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 id="accountTitle"></h5>
                <div class="table-responsive">
                    <table id="ledgerTable" class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Deskripsi</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                                <th class="text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="ledgerBody"></tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="5" class="text-end">Saldo Akhir</th>
                                <th id="saldoAkhir" class="text-end">0</th>
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
        $(document).ready(function() {
            // $('.select2').select2({
            //     width: '100%'
            // });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('report.generalLedgerData.data') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        console.log(res); // Debug

                        // Judul akun dan saldo awal
                        let saldoAwal = res.saldo_awal ?? 0;
                        $('#accountTitle').text(
                            `Akun: ${res.account ?? '-'}`
                        );

                        let rows = '';

                        // Baris saldo awal
                        rows += `<tr class="table-light fw-bold">
                    <td colspan="5" class="text-end">Saldo Awal</td>
                    <td class="text-end">${new Intl.NumberFormat('id-ID').format(saldoAwal)}</td>
                </tr>`;

                        // Baris transaksi
                        const transactions = res.data ?? [];
                        if (transactions.length === 0) {
                            rows +=
                                `<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>`;
                        } else {
                            transactions.forEach(trx => {
                                // Lewati baris saldo awal dari data API
                                if (trx.description === 'Saldo Awal') return;

                                rows += `<tr>
                            <td>${trx.date ?? '-'}</td>
                            <td>${trx.code ?? '-'}</td>
                            <td>${trx.description ?? '-'}</td>
                            <td class="text-end">${trx.debit > 0 ? new Intl.NumberFormat('id-ID').format(trx.debit) : '-'}</td>
                            <td class="text-end">${trx.credit > 0 ? new Intl.NumberFormat('id-ID').format(trx.credit) : '-'}</td>
                            <td class="text-end">${new Intl.NumberFormat('id-ID').format(trx.saldo ?? 0)}</td>
                        </tr>`;
                            });
                        }

                        $('#ledgerBody').html(rows);
                        $('#saldoAkhir').text(new Intl.NumberFormat('id-ID').format(res
                            .saldo_akhir ?? 0));
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON);
                        alert("Terjadi kesalahan: " + xhr.statusText);
                    }
                });
            });
        });
    </script>
@endsection
