@extends('template.master')

@section('settings-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Edit Setting</strong></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('settings.update', 1) }}" method="POST">
                    @csrf @method('PUT')

                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 30%">Nama Setting</th>
                                <th>Akun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" class="table-dark">Transaksi Barang</td>
                            </tr>
                            <tr>
                                <td>Akun Persediaan (Inventory)</td>
                                <td>
                                    <select name="inventory_account_id" id="inventory_account_id"
                                        class="form-select select2 @error('inventory_account_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ old('inventory_account_id', $setting->inventory_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('inventory_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Akun Penyesuaian Barang Bertambah</td>
                                <td>
                                    <select name="stock_in_account_id" id="stock_in_account_id"
                                        class="form-select select2 @error('stock_in_account_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ old('stock_in_account_id', $setting->stock_in_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('stock_in_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Akun Penyesuaian Barang Berkurang</td>
                                <td>
                                    <select name="stock_out_account_id" id="stock_out_account_id"
                                        class="form-select select2 @error('stock_out_account_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ old('stock_out_account_id', $setting->stock_out_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('stock_out_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="table-dark">Pembelian</td>
                            </tr>
                            <tr>
                                <td>Akun Subtotal Pembelian</td>
                                <td>
                                    <select name="purchase_subtotal_account_id" id="purchase_subtotal_account_id"
                                        class="form-select select2 @error('purchase_subtotal_account_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ old('purchase_subtotal_account_id', $setting->purchase_subtotal_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('purchase_subtotal_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Akun Diskon Pembelian</td>
                                <td>
                                    <select name="purchase_discount_account_id" id="purchase_discount_account_id"
                                        class="form-select select2 @error('purchase_discount_account_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ old('purchase_discount_account_id', $setting->purchase_discount_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('purchase_discount_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Akun Pajak Pembelian</td>
                                <td>
                                    <select name="purchase_tax_account_id" id="purchase_tax_account_id"
                                        class="form-select select2 @error('purchase_tax_account_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ old('purchase_tax_account_id', $setting->purchase_tax_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('purchase_tax_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Akun Ongkir Pembelian</td>
                                <td>
                                    <select name="purchase_freight_account_id" id="purchase_freight_account_id"
                                        class="form-select select2 @error('purchase_freight_account_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ old('purchase_freight_account_id', $setting->purchase_freight_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('purchase_freight_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Akun Beban Pembelian</td>
                                <td>
                                    <select name="purchase_expenses_account_id" id="purchase_expenses_account_id"
                                        class="form-select select2 @error('purchase_expenses_account_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ old('purchase_expenses_account_id', $setting->purchase_expenses_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('purchase_expenses_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Akun Grand Total Pembelian</td>
                                <td>
                                    <select name="purchase_grand_total_account_id" id="purchase_grand_total_account_id"
                                        class="form-select select2 @error('purchase_grand_total_account_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ old('purchase_grand_total_account_id', $setting->purchase_grand_total_account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('purchase_grand_total_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
