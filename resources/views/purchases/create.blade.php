@extends('template.master')

@section('purchases-active', 'active')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-3"><strong>Tambah Pembelian</strong></h1>

        <form action="{{ route('purchases.store') }}" method="POST">
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
                    <label>Gudang</label>
                    <select name="warehouse_id" class="form-select select2" required>
                        <option value="">-- Pilih Gudang --</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Supplier</label>
                    <select name="contact_id" class="form-select select2" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($contacts as $contact)
                            <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Diskon %</th>
                        <th>Diskon (Rp)</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="purchaseDetails">
                    <tr>
                        <td>
                            <select name="item_id[]" class="form-select select2" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="qty[]" class="form-control form-control-sm currency-input"></td>
                        <td><input type="text" name="price[]" class="form-control form-control-sm currency-input"></td>
                        <td><input type="text" name="discount_percent[]"
                                class="form-control form-control-sm currency-input"></td>
                        <td><input type="text" name="discount[]" class="form-control form-control-sm currency-input"
                                readonly></td>
                        <td><input type="text" name="total[]" class="form-control form-control-sm currency-input"
                                readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" id="addRow" class="btn btn-secondary btn-sm">Tambah Baris</button>
            <hr>

            <table class="table table-borderless align-middle">
                <tr>
                    <td>
                        <label class="form-label">Subtotal</label>
                        <input type="text" name="subtotal" id="subtotal" class="form-control currency-input" readonly>
                    </td>
                    <td>
                        <label class="form-label">Pajak (%)</label>
                        <input type="text" name="tax_percent" id="tax_percent" class="form-control currency-input">
                    </td>
                    <td>
                        <label class="form-label">Pajak (Rp)</label>
                        <input type="text" name="tax" id="tax" class="form-control currency-input" readonly>
                    </td>
                    <td>
                        <label class="form-label">Ongkir</label>
                        <input type="text" name="freight" id="freight" class="form-control currency-input">
                    </td>
                    <td>
                        <label class="form-label">Biaya Lain</label>
                        <input type="text" name="expense" id="expense" class="form-control currency-input">
                    </td>
                    <td style="display: none;">
                        <label class="form-label">Diskon Faktur</label>
                        <input type="text" name="discount_header" id="discount_header"
                            class="form-control currency-input">
                    </td>
                    <td>
                        <label class="form-label">Grand Total</label>
                        <input type="text" name="grand_total" id="grand_total" class="form-control currency-input"
                            readonly>
                    </td>
                </tr>
            </table>

            <hr>
            <h4>Pembayaran</h4>
            <table class="table table-borderless align-middle">
                <tr>
                    <td>
                        <label class="form-label">Akun Kas/Bank</label>
                        <select name="account_id" class="form-select select2">
                            <option value="">-- Pilih Akun --</option>
                            @foreach ($payment_gateways as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <label class="form-label">Jumlah Dibayar</label>
                        <input type="text" name="payment_amount" id="payment_amount" class="form-control currency-input">
                    </td>
                </tr>
            </table>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <script>
        // Formatter angka Indonesia
        const formatter = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });

        function parseNumber(value) {
            return parseFloat(value.replace(/\./g, '').replace(/,/g, '.')) || 0;
        }

        function formatCurrency(input) {
            let value = parseNumber(input.value);
            input.value = value ? formatter.format(value) : '';
        }

        function hitungTotalRow(row) {
            let qty = parseNumber(row.querySelector('[name="qty[]"]').value);
            let price = parseNumber(row.querySelector('[name="price[]"]').value);
            let discPercent = parseNumber(row.querySelector('[name="discount_percent[]"]').value);
            if (!discPercent || discPercent < 0) discPercent = 0;

            let bruto = qty * price;
            let discFromPercent = bruto * discPercent / 100;
            let total = bruto - discFromPercent;

            row.querySelector('[name="discount[]"]').value = formatter.format(discFromPercent);
            row.querySelector('[name="total[]"]').value = formatter.format(total);
            return total;
        }

        function hitungGrandTotal() {
            let subtotal = 0;
            document.querySelectorAll('#purchaseDetails tr').forEach(row => {
                subtotal += hitungTotalRow(row);
            });

            let discountHeader = parseNumber(document.getElementById('discount_header').value);
            if (!discountHeader || discountHeader < 0) discountHeader = 0;

            let dpp = subtotal - discountHeader;
            if (dpp < 0) dpp = 0;

            let taxPercent = parseNumber(document.getElementById('tax_percent').value);
            if (!taxPercent || taxPercent < 0) taxPercent = 0;

            let tax = dpp * taxPercent / 100;
            let freight = parseNumber(document.getElementById('freight').value);
            let expense = parseNumber(document.getElementById('expense').value);

            let grandTotal = dpp + tax + freight + expense;

            document.getElementById('subtotal').value = formatter.format(subtotal);
            document.getElementById('tax').value = formatter.format(tax);
            document.getElementById('grand_total').value = formatter.format(grandTotal);
        }

        document.getElementById('addRow').addEventListener('click', function() {
            let row = document.querySelector('#purchaseDetails tr').cloneNode(true);
            $(row).find('.select2').removeClass('select2-hidden-accessible').next(".select2").remove();
            row.querySelectorAll('input').forEach(input => input.value = '');
            row.querySelector('select').selectedIndex = 0;
            document.getElementById('purchaseDetails').appendChild(row);
            $(row).find('.select2').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih --',
                width: '100%'
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeRow')) {
                e.target.closest('tr').remove();
                hitungGrandTotal();
            }
        });

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('currency-input')) {
                formatCurrency(e.target);
                hitungGrandTotal();
            }
        });

        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('.currency-input').forEach(input => {
                input.value = parseNumber(input.value);
            });
        });

        hitungGrandTotal();
    </script>
@endsection
