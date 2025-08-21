@extends('template.master')

@section('item_transactions-active', 'active')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3"><strong>Tambah Transaksi Barang</strong></h1>

    <form action="{{ route('item_transactions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
        </div>
        <div class="mb-3">
            <label>Gudang</label>
            <select name="warehouse_id" class="form-select select2" required>
                <option value="">-- Pilih Gudang --</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="description" class="form-control" required>{{ old('description') }}</textarea>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="transactionDetails">
                <tr>
                    <td>
                        <select name="item_id[]" class="form-select select2" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="in[]" class="form-control form-control-sm currency-input">
                    </td>
                    <td>
                        <input type="text" name="out[]" class="form-control form-control-sm currency-input">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" id="addRow" class="btn btn-secondary btn-sm">Tambah Baris</button>
        <hr>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<script>
    // Formatter angka Indonesia
    const formatter = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });

    function formatCurrency(input) {
        let value = input.value.replace(/\./g, '').replace(/,/g, '.');
        if (isNaN(value) || value === '') {
            input.value = '';
            return;
        }
        input.value = formatter.format(value);
    }

    document.getElementById('addRow').addEventListener('click', function() {
        let row = document.querySelector('#transactionDetails tr').cloneNode(true);

        $(row).find('.select2').removeClass('select2-hidden-accessible').next(".select2").remove();

        row.querySelectorAll('input').forEach(input => input.value = '');
        row.querySelector('select').selectedIndex = 0;

        document.getElementById('transactionDetails').appendChild(row);

        $(row).find('.select2').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih --',
            allowClear: true,
            width: '100%'
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('currency-input')) {
            formatCurrency(e.target);
        }
    });

    document.querySelector('form').addEventListener('submit', function() {
        document.querySelectorAll('.currency-input').forEach(input => {
            input.value = input.value.replace(/\./g, '').replace(/,/g, '.');
        });
    });
</script>
@endsection
