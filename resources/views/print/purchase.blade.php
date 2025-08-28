<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Faktur Pembelian - {{ $purchase->code }}</title>
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
            <h3><strong>FAKTUR PEMBELIAN</strong></h3>
        </div>

        <p><strong>Nomor Faktur:</strong> {{ $purchase->code }}</p>
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($purchase->date)->locale('id')->translatedFormat('l, d/m/Y') }}</p>
        <p><strong>Supplier:</strong> {{ $purchase->contact->prefix }} {{ $purchase->contact->name }} ({{ $purchase->contact->organization_name }})</p>

        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr class="table-primary">
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Diskon %</th>
                        <th class="text-end">Diskon</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->details as $detail)
                        <tr>
                            <td>{{ $detail->item->code }}</td>
                            <td>{{ $detail->item->name }}</td>
                            <td class="text-end">{{ number_format($detail->qty, 0, ',', '.') }} {{ $detail->item->mainUnit->name }}</td>
                            <td class="text-end">{{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($detail->discount_percent, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($detail->discount, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($detail->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Subtotal</th>
                        <th class="text-end">{{ number_format($purchase->subtotal, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Diskon ({{ number_format($purchase->discount_percent, 2, ',', '.') }}%)</th>
                        <th class="text-end">{{ number_format($purchase->discount, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Ongkos Kirim</th>
                        <th class="text-end">{{ number_format($purchase->freight, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Biaya Lain</th>
                        <th class="text-end">{{ number_format($purchase->expense, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Pajak ({{ number_format($purchase->tax_percent, 0, ',', '.') }}%)</th>
                        <th class="text-end">{{ number_format($purchase->tax, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-dark">
                        <th colspan="6" class="text-end">Grand Total</th>
                        <th class="text-end">{{ number_format($purchase->grand_total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Tanda Tangan --}}
        <div class="row mt-5">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p><strong>Mengetahui,</strong></p>
                <div class="signature">
                    <br><br><br>
                    <p><strong>{{ $purchase->user->name }}</strong></p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
