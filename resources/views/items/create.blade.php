@extends('template.master')

@section('items-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Tambah Barang</strong></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('items.store') }}" method="POST">
                    @csrf

                    {{-- Kode, Nama, Barcode --}}
                    <div class="mb-3">
                        <label for="code" class="form-label">Kode Barang</label>
                        <input type="text" name="code" id="code"
                            class="form-control @error('code') is-invalid @enderror"
                            value="{{ old('code', $item->code ?? '') }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Barang</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $item->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="barcode" class="form-label">Barcode Barang</label>
                        <input type="text" name="barcode" id="barcode"
                            class="form-control @error('barcode') is-invalid @enderror"
                            value="{{ old('barcode', $item->barcode ?? '') }}">
                        @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-3">
                        <label for="item_category_id" class="form-label">Kategori Barang</label>
                        <select name="item_category_id" id="item_category_id"
                            class="form-select select2 @error('item_category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('item_category_id', $item->item_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Satuan --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="main_unit_id" class="form-label">Satuan</label>
                            <select name="main_unit_id" id="main_unit_id"
                                class="form-select select2 @error('main_unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Satuan Utama --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ old('main_unit_id', $item->main_unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('main_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- <div class="col-md-6">
                            <label for="secondary_unit_id" class="form-label">Satuan Besar</label>
                            <select name="secondary_unit_id" id="secondary_unit_id"
                                class="form-select select2 @error('secondary_unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Satuan Sekunder --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ old('secondary_unit_id', $item->secondary_unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('secondary_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}
                    </div>

                    {{-- Conversion Rate --}}
                    {{-- <div class="mb-3">
                        <label for="conversion_rate" class="form-label">Conversion Rate</label>
                        <input type="number" step="0.01" id="conversion_rate" name="conversion_rate"
                            class="form-control" value="{{ old('conversion_rate', $item->conversion_rate ?? 1) }}">
                    </div> --}}

                    {{-- Harga Beli --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="purchase_price_main" class="form-label">Harga Beli
                                {{-- (Satuan Kecil) --}}
                            </label>
                            <input type="text" id="purchase_price_main" name="purchase_price_main" class="form-control"
                                value="{{ old('purchase_price_main', $item->purchase_price_main ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="selling_price_main" class="form-label">Harga Jual
                                {{-- (Satuan Kecil) --}}
                            </label>
                            <input type="text" id="selling_price_main" name="selling_price_main" class="form-control"
                                value="{{ old('selling_price_main', $item->selling_price_main ?? '') }}">
                        </div>
                        {{-- <div class="col-md-6">
                            <label for="purchase_price_secondary" class="form-label">Harga Beli (Satuan Besar)</label>
                            <input type="text" id="purchase_price_secondary" name="purchase_price_secondary"
                                class="form-control"
                                value="{{ old('purchase_price_secondary', $item->purchase_price_secondary ?? '') }}">
                        </div> --}}
                    </div>

                    {{-- Harga Jual --}}
                    {{-- <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="selling_price_secondary" class="form-label">Harga Jual (Satuan Besar)</label>
                            <input type="text" id="selling_price_secondary" name="selling_price_secondary"
                                class="form-control" readonly
                                value="{{ old('selling_price_secondary', $item->selling_price_secondary ?? '') }}">
                        </div>
                    </div> --}}

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description', $item->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Apakah bisa dihitung (is_countable) --}}
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="is_countable" name="is_countable"
                            value="1" {{ old('is_countable', $item->is_countable ?? 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_countable">Apa barang ini dapat dihitung ?</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // function updatePurchaseMain() {
        //     const secInput = document.getElementById('purchase_price_secondary');
        //     const mainInput = document.getElementById('purchase_price_main');
        //     const conversion = parseFloat(document.getElementById('conversion_rate').value) || 1;

        //     const sec = parseFloat(secInput.value) || 0;
        //     mainInput.value = (sec / conversion).toFixed(0); // hasil langsung angka bersih
        // }

        // function updateSellingSecondary() {
        //     const mainInput = document.getElementById('selling_price_main');
        //     const secInput = document.getElementById('selling_price_secondary');
        //     const conversion = parseFloat(document.getElementById('conversion_rate').value) || 1;

        //     const main = parseFloat(mainInput.value) || 0;
        //     secInput.value = (main * conversion).toFixed(0); // hasil langsung angka bersih
        // }

        // // Event listener
        // document.getElementById('purchase_price_secondary').addEventListener('input', updatePurchaseMain);
        // document.getElementById('selling_price_main').addEventListener('input', updateSellingSecondary);
        // document.getElementById('conversion_rate').addEventListener('input', () => {
        //     updatePurchaseMain();
        //     updateSellingSecondary();
        // });
    </script>
@endsection
