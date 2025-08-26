@extends('template.master')

@section('contacts-active', 'active')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-3"><strong>Tambah Pelunasan Hutang</strong></h1>

        <form action="{{ route('purchasePayments.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Kode</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $code) }}" readonly>
                </div>
                <div class="col-md-3">
                    <label>Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}"
                        required>
                </div>
                <div class="col-md-3">
                    <label>Supplier</label>
                    @php $contact = $purchases->first()?->contact; @endphp
                    <input type="hidden" name="contact_id" value="{{ $contact?->id }}">
                    <input type="text" class="form-control" value="{{ $contact?->name }}" readonly>
                </div>
                <div class="col-md-3">
                    <label>Metode Pembayaran</label>
                    <select name="account_id" class="form-select select2" required>
                        <option value="">-- Pilih Kas/Bank --</option>
                        @foreach ($payment_gateways as $pg)
                            <option value="{{ $pg->id }}">{{ $pg->code }} - {{ $pg->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Kode Faktur</th>
                        {{-- <th>Tanggal</th> --}}
                        <th>Tagihan</th>
                        <th>Terbayar</th>
                        <th>Sisa</th>
                        <th>Pelunasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="payment-rows">
                    @foreach ($purchases as $purchase)
                        <tr>
                            <td>
                                {{ $purchase->code }}
                                <input type="hidden" name="details[{{ $loop->index }}][purchase_id]" value="{{ $purchase->id }}">
                            </td>
                            {{-- <td>{{ $purchase->date }}</td> --}}
                            <td class="text-end">{{ number_format($purchase->grand_total, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($purchase->paid, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($purchase->remaining, 0, ',', '.') }}</td>
                            <td>
                                <input type="text" name="details[{{ $loop->index }}][total]" class="form-control form-control-sm currency-input"
                                    placeholder="0">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-row">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="5" class="text-end">Total Pelunasan</th>
                        <th>
                            <input type="text" name="grand_total" id="grand_total" class="form-control currency-input"
                                readonly>
                        </th>
                    </tr>
                </tfoot>
            </table>


            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('purchasePayments.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>

@endsection

@section('script')
    <script>
        const formatter = new Intl.NumberFormat('id-ID');

        function parseNumber(value) {
            return parseFloat(value.replace(/\./g, '').replace(/,/g, '.')) || 0;
        }

        function formatCurrency(input) {
            let value = parseNumber(input.value);
            input.value = value ? formatter.format(value) : '';
        }

        function hitungGrandTotal() {
            let total = 0;
            document.querySelectorAll('input[name^="details"][name$="[total]"]').forEach(el => {
                total += parseNumber(el.value);
            });
            document.getElementById('grand_total').value = formatter.format(total);
        }

        // Format + Hitung Total
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('currency-input')) {
                formatCurrency(e.target);
                hitungGrandTotal();
            }
        });

        // Hapus baris
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
                hitungGrandTotal();
            }
        });

        // Konversi angka sebelum submit
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('.currency-input').forEach(input => {
                input.value = parseNumber(input.value);
            });
        });
    </script>
@endsection
