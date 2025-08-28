<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Mutasi Barang - {{ $itemTransaction->code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 14px;
            color: #000;
            background: #fff;
        }
        .table th, .table td {
            padding: 6px 10px;
        }
        .header-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .signature {
            margin-top: 60px;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container my-4">
        <div class="header-title">
            <h3><strong>MUTASI BARANG</strong></h3>
        </div>

        <p><strong>Nomor Faktur:</strong> {{ $itemTransaction->code }}</p>
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($itemTransaction->date)->locale('id')->translatedFormat('l, d/m/Y') }}</p>
        <p><strong>Keterangan:</strong> {{ $itemTransaction->description }}</p>
        <p><strong>Gudang:</strong> {{ $itemTransaction->warehouse->name }}</p>

        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr class="table-primary">
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th class="text-end">Masuk</th>
                        <th class="text-end">Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($itemTransaction->details as $detail)
                        <tr>
                            <td>{{ $detail->item->code }}</td>
                            <td>{{ $detail->item->name }}</td>
                            <td>{{ $detail->item->mainUnit->name }}</td>
                            <td class="text-end">{{ $detail->in ? number_format($detail->in, 0, ',', '.') : '-' }}</td>
                            <td class="text-end">{{ $detail->out ? number_format($detail->out, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="3">Total</th>
                        <th class="text-end">{{ number_format($itemTransaction->details->sum('in'), 0, ',', '.') }}</th>
                        <th class="text-end">{{ number_format($itemTransaction->details->sum('out'), 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Tanda Tangan --}}
        <div class="row mt-3">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p><strong>Mengetahui,</strong></p>
                <div class="signature">
                    <br><br><br>
                    <p><strong>{{ $itemTransaction->user->name }}</strong></p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
