@extends('template.master')

@section('orders-active', 'active')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3"><strong>Tambah Pesanan</strong></h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        {{-- <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="code" class="form-control" value="{{ $code }}" readonly>
        </div> --}}
        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
        </div>
        <div class="mb-3">
            <label>Jenis Order</label>
            <select name="type" class="form-select select2" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="purchase_quotation">Purchase Quotation</option>
                <option value="sales_quotation">Sales Quotation</option>
                <option value="purchase_order">Purchase Order</option>
                <option value="sales_order">Sales Order</option>
                <option value="delivery_order">Delivery Order</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Kontak</label>
            <select name="contact_id" class="form-select select2" required>
                <option value="">-- Pilih Kontak --</option>
                @foreach ($contacts as $contact)
                    <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Diskon %</th>
                    <th>Diskon</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="orderDetails">
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
                    <td><input type="text" name="discount_percent[]" class="form-control form-control-sm currency-input"></td>
                    <td><input type="text" name="discount[]" class="form-control form-control-sm currency-input"></td>
                    <td><input type="text" name="total[]" class="form-control form-control-sm currency-input" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" id="addRow" class="btn btn-secondary btn-sm">Tambah Baris</button>
        <hr>

        <div class="mb-3">
            <label>Subtotal</label>
            <input type="text" name="subtotal" id="subtotal" class="form-control currency-input" readonly>
        </div>
        <div class="mb-3">
            <label>Pajak (%)</label>
            <input type="text" name="tax_percent" id="tax_percent" class="form-control currency-input">
        </div>
        <div class="mb-3">
            <label>Pajak (Rp)</label>
            <input type="text" name="tax" id="tax" class="form-control currency-input" readonly>
        </div>
        <div class="mb-3">
            <label>Ongkir</label>
            <input type="text" name="freight" id="freight" class="form-control currency-input">
        </div>
        <div class="mb-3">
            <label>Biaya Lain</label>
            <input type="text" name="expense" id="expense" class="form-control currency-input">
        </div>
        <div class="mb-3">
            <label>Grand Total</label>
            <input type="text" name="grand_total" id="grand_total" class="form-control currency-input" readonly>
        </div>

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
        let disc = parseNumber(row.querySelector('[name="discount[]"]').value);

        let bruto = qty * price;
        let discFromPercent = bruto * discPercent / 100;
        let totalDisc = discFromPercent + disc;
        let total = bruto - totalDisc;

        row.querySelector('[name="discount[]"]').value = formatter.format(totalDisc);
        row.querySelector('[name="total[]"]').value = formatter.format(total);
        return total;
    }

    function hitungGrandTotal() {
        let subtotal = 0;
        document.querySelectorAll('#orderDetails tr').forEach(row => {
            subtotal += hitungTotalRow(row);
        });
        let taxPercent = parseNumber(document.getElementById('tax_percent').value);
        let freight = parseNumber(document.getElementById('freight').value);
        let expense = parseNumber(document.getElementById('expense').value);
        let tax = subtotal * taxPercent / 100;
        let grandTotal = subtotal + tax + freight + expense;

        document.getElementById('subtotal').value = formatter.format(subtotal);
        document.getElementById('tax').value = formatter.format(tax);
        document.getElementById('grand_total').value = formatter.format(grandTotal);
    }

    document.getElementById('addRow').addEventListener('click', function() {
        let row = document.querySelector('#orderDetails tr').cloneNode(true);
        $(row).find('.select2').removeClass('select2-hidden-accessible').next(".select2").remove();
        row.querySelectorAll('input').forEach(input => input.value = '');
        row.querySelector('select').selectedIndex = 0;
        document.getElementById('orderDetails').appendChild(row);
        $(row).find('.select2').select2({ theme: 'bootstrap4', placeholder: '-- Pilih --', width: '100%' });
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
