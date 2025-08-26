@extends('template.master')

@section('sales-active', 'active')

@section('content')
    <div class="container-fluid">

        <div class="card mb-3">
            <div class="card-body">
                <h2><strong>FAKTUR PENJUALAN</strong></h2>
                <br>
                <p><strong>Kode:</strong> {{ $sale->code }}</p>
                <p><strong>Tanggal:</strong>
                    {{ \Carbon\Carbon::parse($sale->date)->locale('id')->translatedFormat('l, d/m/Y') }}</p>
                <p><strong>Customer:</strong> {{ $sale->contact->prefix }} {{ $sale->contact->name }}
                    ({{ $sale->contact->organization_name }})</p>
                <br>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
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
                            @foreach ($sale->details as $detail)
                                <tr>
                                    <td>{{ $detail->item->code }}</td>
                                    <td>{{ $detail->item->name }}</td>
                                    <td class="text-end">{{ number_format($detail->qty, 0, ',', '.') }}
                                        {{ $detail->item->mainUnit->name }}</td>
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
                                <th class="text-end">{{ number_format($sale->subtotal, 0, ',', '.') }}</th>
                            </tr>
                            <tr class="table-secondary">
                                <th colspan="6" class="text-end">Diskon
                                    ({{ number_format($sale->discount_percent, 2, ',', '.') }}%)</th>
                                <th class="text-end">{{ number_format($sale->discount, 0, ',', '.') }}</th>
                            </tr>
                            <tr class="table-secondary">
                                <th colspan="6" class="text-end">Ongkos Kirim</th>
                                <th class="text-end">{{ number_format($sale->freight, 0, ',', '.') }}</th>
                            </tr>
                            <tr class="table-secondary">
                                <th colspan="6" class="text-end">Biaya Lain</th>
                                <th class="text-end">{{ number_format($sale->expense, 0, ',', '.') }}</th>
                            </tr>
                            <tr class="table-secondary">
                                <th colspan="6" class="text-end">Pajak
                                    ({{ number_format($sale->tax_percent, 0, ',', '.') }}%)</th>
                                <th class="text-end">{{ number_format($sale->tax, 0, ',', '.') }}</th>
                            </tr>
                            <tr class="table-dark">
                                <th colspan="6" class="text-end">Grand Total</th>
                                <th class="text-end">{{ number_format($sale->grand_total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <hr>

                @if ($sale->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-info">
                                <tr>
                                    <th>Tanggal Pelunasan</th>
                                    <th>Faktur Pelunasan</th>
                                    <th>Akun</th>
                                    <th class="text-end">Jumlah Pelunasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->payments as $paymentDetail)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($paymentDetail->salesPayment->date)->locale('id')->translatedFormat('l, d/m/Y') }}
                                        </td>
                                        <td>{{ $paymentDetail->salesPayment->code }}</td>
                                        <td>{{ $paymentDetail->salesPayment->account->code }} - {{ $paymentDetail->salesPayment->account->name }}</td>
                                        <td class="text-end">{{ number_format($paymentDetail->total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-secondary">
                                    <th colspan="3" class="text-end">Total Pembayaran</th>
                                    <th class="text-end">
                                        {{ number_format($sale->paid, 0, ',', '.') }}
                                    </th>
                                </tr>
                                <tr class="table-dark">
                                    <th colspan="3" class="text-end">Sisa Hutang</th>
                                    <th class="text-end">
                                        {{ number_format($sale->remaining, 0, ',', '.') }}
                                    </th>
                                </tr>
                                <tr class="table-dark">
                                    <th colspan="3" class="text-end">Status</th>
                                    <th class="text-end">
                                        {{ $sale->status }}
                                    </th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                @else
                    <p>Belum ada pelunasan dilakukan untuk faktur ini.</p>
                @endif

            </div>

            <a href="{{ route('sales.index') }}" class="btn btn-secondary mt-3">Kembali</a>
        </div>
    @endsection
