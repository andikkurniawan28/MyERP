@extends('template.master')

@section('journals-active', 'active')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-3"><strong>Detil Jurnal</strong></h1>

        <div class="card mb-3">
            <div class="card-body">
                <p>
                    <strong>Tanggal:</strong>
                    {{ \Carbon\Carbon::parse($journal->date)->locale('id')->translatedFormat('l, d/m/Y') }}
                </p>
                <p><strong>Keterangan:</strong> {{ $journal->description }}</p>
                <p><strong>User:</strong> {{ $journal->user->name }}</p>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr class="table-primary">
                                <th>Akun</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($journal->details as $detail)
                                <tr>
                                    <td>{{ $detail->account->code }} - {{ $detail->account->name }}</td>
                                    <td class="text-end">{{ $detail->debit ? number_format($detail->debit, 0, ',', '.') : '-' }}</td>
                                    <td class="text-end">{{ $detail->credit ? number_format($detail->credit, 0, ',', '.') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary">
                            <th class="py-2 px-3">Total</th>
                            <th class="text-end">{{ number_format($journal->debit, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($journal->credit, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <a href="{{ route('journals.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
@endsection
