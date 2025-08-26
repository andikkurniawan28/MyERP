@extends('template.master')

@section('salesPayments-active', 'active')

@section('content')
    <div class="container-fluid">

        <div class="card mb-3">
            <div class="card-body">
                <h2><strong>FAKTUR PELUNASAN PIUTANG</strong></h2>
                <br>
                <p><strong>Kode:</strong> {{ $salesPayment->code }}</p>
                <p><strong>Tanggal:</strong>
                    {{ \Carbon\Carbon::parse($salesPayment->date)->locale('id')->translatedFormat('l, d/m/Y') }}
                </p>
                <p><strong>Customer:</strong>
                    {{ $salesPayment->contact->prefix }} {{ $salesPayment->contact->name }}
                    ({{ $salesPayment->contact->organization_name }})
                </p>
                <p><strong>Dibayarkan melalui:</strong> {{ $salesPayment->account->code }} - {{ $salesPayment->account->name }}</p>
                <br>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr class="table-primary">
                                <th>Faktur Pembelian</th>
                                {{-- <th>Dibayarkan melalui</th> --}}
                                <th class="text-end">Jumlah Pelunasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salesPayment->details as $detail)
                                <tr>
                                    <td>{{ $detail->sales->code ?? '-' }}</td>
                                    {{-- <td>{{ $detail->account->code }} - {{ $detail->account->name }}</td> --}}
                                    <td class="text-end">{{ number_format($detail->total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-dark">
                                <th colspan="1" class="text-end">Grand Total</th>
                                <th class="text-end">{{ number_format($salesPayment->grand_total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            <a href="{{ route('salesPayments.index') }}" class="btn btn-secondary mt-3">Kembali</a>
        </div>
    </div>
@endsection
