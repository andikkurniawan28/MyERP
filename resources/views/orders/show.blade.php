@extends('template.master')

@section('orders-active', 'active')

@section('content')
<div class="container-fluid">
    {{-- <h1 class="h3 mb-3"><strong>Detail Pesanan</strong></h1> --}}

    <div class="card mb-3">
        <div class="card-body">
            <h2><strong>{{ strtoupper(str_replace('_', ' ', $order->type)) }}</strong></h2>
            <br>
            <p><strong>Kode:</strong> {{ $order->code }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($order->date)->locale('id')->translatedFormat('l, d/m/Y') }}</p>
            <p><strong>Kepada:</strong> {{ $order->contact->prefix }} {{ $order->contact->name }} ({{ $order->contact->organization_name }})</p>
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
                    @foreach ($order->details as $detail)
                        <tr>
                            <td>{{ $detail->item->code }}</td>
                            <td>{{ $detail->item->name }}</td>
                            <td class="text-end">{{ number_format($detail->qty, 0, ',', '.') }}</td>
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
                        <th class="text-end">{{ number_format($order->subtotal, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Diskon</th>
                        <th class="text-end">{{ number_format($order->discount, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Ongkos Kirim</th>
                        <th class="text-end">{{ number_format($order->freight, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Biaya Lain</th>
                        <th class="text-end">{{ number_format($order->expense, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Pajak ({{ number_format($order->tax_percent, 0, ',', '.') }}%)</th>
                        <th class="text-end">{{ number_format($order->tax, 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-dark">
                        <th colspan="6" class="text-end">Grand Total</th>
                        <th class="text-end">{{ number_format($order->grand_total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
