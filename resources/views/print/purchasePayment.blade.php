<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Pelunasan Hutang - {{ $purchasePayment->code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 14px;
            color: #000;
            background: #fff;
        }

        .table th,
        .table td {
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
            <h3><strong>FAKTUR PELUNASAN HUTANG</strong></h3>
        </div>

        <p><strong>Kode:</strong> {{ $purchasePayment->code }}</p>
        <p><strong>Tanggal:</strong>
            {{ \Carbon\Carbon::parse($purchasePayment->date)->locale('id')->translatedFormat('l, d/m/Y') }}</p>
        <p><strong>Supplier:</strong> {{ $purchasePayment->contact->prefix }} {{ $purchasePayment->contact->name }}
            ({{ $purchasePayment->contact->organization_name }})</p>
        <p><strong>Dibayarkan melalui:</strong> {{ $purchasePayment->account->code }} -
            {{ $purchasePayment->account->name }}</p>

        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr class="table-primary">
                        <th>Faktur Pembelian</th>
                        <th class="text-end">Jumlah Pelunasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchasePayment->details as $detail)
                        <tr>
                            <td>{{ $detail->purchase->code ?? '-' }}</td>
                            <td class="text-end">{{ number_format($detail->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-dark">
                        <th class="text-end">Grand Total</th>
                        <th class="text-end">{{ number_format($purchasePayment->grand_total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Tanda Tangan --}}
        <div class="row mt-3">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p><strong>Dibuat oleh,</strong></p>
                <div class="signature">
                    <br><br><br>
                    <p><strong>{{ $purchasePayment->user->name ?? '-' }}</strong></p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
