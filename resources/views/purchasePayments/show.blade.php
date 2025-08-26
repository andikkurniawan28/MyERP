@extends('template.master')

@section('purchasePayments-active', 'active')

@section('content')
    <div class="container-fluid">

        <div class="card mb-3">
            <div class="card-body">
                <h2><strong>FAKTUR PELUNASAN HUTANG</strong></h2>
                <br>
                <p><strong>Kode:</strong> {{ $purchasePayment->code }}</p>
                <p><strong>Tanggal:</strong>
                    {{ \Carbon\Carbon::parse($purchasePayment->date)->locale('id')->translatedFormat('l, d/m/Y') }}
                </p>
                <p><strong>Supplier:</strong>
                    {{ $purchasePayment->contact->prefix }} {{ $purchasePayment->contact->name }}
                    ({{ $purchasePayment->contact->organization_name }})
                </p>
                <br>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr class="table-primary">
                                <th>Faktur Pembelian</th>
                                <th>Dibayarkan melalui</th>
                                <th class="text-end">Jumlah Pelunasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchasePayment->details as $detail)
                                <tr>
                                    <td>{{ $detail->purchase->code ?? '-' }}</td>
                                    <td>{{ $detail->account->code }} - {{ $detail->account->name }}</td>
                                    <td class="text-end">{{ number_format($detail->total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-dark">
                                <th colspan="2" class="text-end">Grand Total</th>
                                <th class="text-end">{{ number_format($purchasePayment->grand_total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            <a href="{{ route('purchasePayments.index') }}" class="btn btn-secondary mt-3">Kembali</a>
        </div>
    </div>
@endsection
