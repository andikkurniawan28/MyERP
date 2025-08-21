@extends('template.master')

@section('items-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Tambah Barang</strong></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('items.store') }}" method="POST">
                    @csrf

                    {{-- Kode Barang --}}
                    <div class="mb-3">
                        <label for="code" class="form-label">Kode Barang</label>
                        <input type="text" name="code" id="code"
                            class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nama Barang --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Barang</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Barcode Barang --}}
                    <div class="mb-3">
                        <label for="barcode" class="form-label">Barcode Barang</label>
                        <input type="text" name="barcode" id="barcode"
                            class="form-control @error('barcode') is-invalid @enderror" value="{{ old('barcode') }}" required>
                        @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori Barang --}}
                    <div class="mb-3">
                        <label for="item_category_id" class="form-label">Kategori Barang</label>
                        <select name="item_category_id" id="item_category_id"
                            class="form-select select2 @error('item_category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('item_category_id') == $category->id ? 'selected' : '' }}>
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
                        <div class="col-md-6">
                            <label for="main_unit_id" class="form-label">Satuan Utama</label>
                            <select name="main_unit_id" id="main_unit_id"
                                class="form-select select2 @error('main_unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Satuan Utama --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ old('main_unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('main_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="secondary_unit_id" class="form-label">Satuan Sekunder</label>
                            <select name="secondary_unit_id" id="secondary_unit_id"
                                class="form-select select2 @error('secondary_unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Satuan Sekunder --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ old('secondary_unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('secondary_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Conversion Rate --}}
                    <div class="mb-3">
                        <label for="conversion_rate" class="form-label">Conversion Rate</label>
                        <input type="number" name="conversion_rate" id="conversion_rate" step="0.01"
                            class="form-control @error('conversion_rate') is-invalid @enderror"
                            value="{{ old('conversion_rate', 1) }}" required>
                        @error('conversion_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Harga --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="purchase_price_secondary" class="form-label">Harga Beli (Satuan Sekunder)</label>
                            <input type="number" name="purchase_price_secondary" id="purchase_price_secondary"
                                step="0.01" class="form-control @error('purchase_price_secondary') is-invalid @enderror"
                                value="{{ old('purchase_price_secondary') }}" required>
                            @error('purchase_price_secondary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="selling_price_secondary" class="form-label">Harga Jual (Satuan Sekunder)</label>
                            <input type="number" name="selling_price_secondary" id="selling_price_secondary" step="0.01"
                                class="form-control @error('selling_price_secondary') is-invalid @enderror"
                                value="{{ old('selling_price_secondary') }}">
                            @error('selling_price_secondary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="purchase_price_main" class="form-label">Harga Beli (Satuan Utama)</label>
                            <input type="number" name="purchase_price_main" id="purchase_price_main" step="0.01"
                                class="form-control @error('purchase_price_main') is-invalid @enderror"
                                value="{{ old('purchase_price_main') }}" required>
                            @error('purchase_price_main')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="selling_price_main" class="form-label">Harga Jual (Satuan Utama)</label>
                            <input type="number" name="selling_price_main" id="selling_price_main" step="0.01"
                                class="form-control @error('selling_price_main') is-invalid @enderror"
                                value="{{ old('selling_price_main') }}">
                            @error('selling_price_main')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
