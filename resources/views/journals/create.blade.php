@extends('template.master')

@section('journals-active', 'active')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-3"><strong>Tambah Jurnal</strong></h1>

        <form action="{{ route('journals.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Kode</label>
                <input type="code" name="code" class="form-control" value="{{ $code }}" readonly>
            </div>
            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>
            <div class="mb-3">
                <label>Keterangan</label>
                <textarea name="description" class="form-control" required>{{ old('description') }}</textarea>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Akun</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="journalDetails">
                    <tr>
                        <td>
                            <select name="account_id[]" class="form-select select2" width="100%" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="debit[]" class="form-control form-control-sm currency-input">
                        </td>
                        <td>
                            <input type="text" name="credit[]" class="form-control form-control-sm currency-input">
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
        // Formatter untuk locale Indonesia
        const formatter = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });

        // Fungsi format angka ketika user mengetik
        function formatCurrency(input) {
            let value = input.value.replace(/\./g, '').replace(/,/g, '.'); // hapus pemisah ribuan & ubah koma ke titik
            if (isNaN(value) || value === '') {
                input.value = '';
                return;
            }
            input.value = formatter.format(value);
        }

        // Tambah baris baru
        document.getElementById('addRow').addEventListener('click', function() {
            let row = document.querySelector('#journalDetails tr').cloneNode(true);

            // Hapus elemen select2 hasil clone (biar nggak dobel)
            $(row).find('.select2').removeClass('select2-hidden-accessible').next(".select2").remove();

            // Reset input & select
            row.querySelectorAll('input').forEach(input => input.value = '');
            row.querySelector('select').selectedIndex = 0;

            document.getElementById('journalDetails').appendChild(row);

            // Re-init select2
            $(row).find('.select2').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih --',
                allowClear: true,
                width: '100%'
            });
        });

        // Hapus baris
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeRow')) {
                e.target.closest('tr').remove();
            }
        });

        // Format currency saat user mengetik
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('currency-input')) {
                formatCurrency(e.target);
            }
        });

        // Sebelum submit, ubah kembali ke angka murni (tanpa format)
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('.currency-input').forEach(input => {
                input.value = input.value.replace(/\./g, '').replace(/,/g, '.');
            });
        });
    </script>
@endsection
