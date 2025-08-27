@extends('template.master')

@section('item_transactions-active', 'active')

@section('content')
<div class="container-fluid">
    {{-- <h1 class="h3 mb-3"><strong>Detail Transaksi Barang</strong></h1> --}}

    <div class="card mb-3">
        <div class="card-body">
                <h2><strong>MUTASI BARANG</strong></h2>
                <br>
            <p><strong>Kode:</strong> {{ $itemTransaction->code }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($itemTransaction->date)->locale('id')->translatedFormat('l, d/m/Y') }}</p>
            <p><strong>Keterangan:</strong> {{ $itemTransaction->description }}</p>
            <p><strong>Gudang:</strong> {{ $itemTransaction->warehouse->name }}</p>
            <p><strong>User:</strong> {{ $itemTransaction->user->name }}</p>
            <br>

            <div class="table-responsive">
                <table class="table table-bordered mb-0">
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
        </div>
    </div>

    <a href="{{ route('item_transactions.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
