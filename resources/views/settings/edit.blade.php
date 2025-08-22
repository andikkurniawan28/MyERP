@extends('template.master')

@section('settings-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Edit Setting</strong></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('settings.update', 1) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="inventory_account_id" class="form-label">Akun Persediaan (Inventory)</label>
                        <select name="inventory_account_id" id="inventory_account_id"
                            class="form-select select2 @error('inventory_account_id') is-invalid @enderror" required>
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
                    </div>

                    <div class="mb-3">
                        <label for="stock_in_account_id" class="form-label">Akun Penyesuaian Barang Bertambah</label>
                        <select name="stock_in_account_id" id="stock_in_account_id"
                            class="form-select select2 @error('stock_in_account_id') is-invalid @enderror" required>
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
                    </div>

                    <div class="mb-3">
                        <label for="stock_out_account_id" class="form-label">Akun Penyesuaian Barang Berkurang</label>
                        <select name="stock_out_account_id" id="stock_out_account_id"
                            class="form-select select2 @error('stock_out_account_id') is-invalid @enderror" required>
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
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
