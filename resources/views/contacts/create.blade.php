@extends('template.master')

@section('contacts-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Tambah Kontak</strong></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('contacts.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="organization_name" class="form-label">Nama Organisasi</label>
                        <input type="text" name="organization_name" id="organization_name" class="form-control"
                            value="{{ old('organization_name') }}">
                    </div>

                    <div class="mb-3">
                        <label for="position" class="form-label">Jabatan</label>
                        <input type="text" name="position" id="position" class="form-control"
                            value="{{ old('position') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="phone" class="form-label">Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="whatsapp" class="form-label">WhatsApp</label>
                            <input type="text" name="whatsapp" id="whatsapp" class="form-control"
                                value="{{ old('whatsapp') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="npwp" class="form-label">NPWP</label>
                        <input type="text" name="npwp" id="npwp" class="form-control"
                            value="{{ old('npwp') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="birthday" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birthday" id="birthday" class="form-control"
                                value="{{ old('birthday') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Tipe</label>
                            <select name="type" id="type"
                                class="form-select select2 @error('type') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="supplier" {{ old('type') == 'supplier' ? 'selected' : '' }}>Supplier
                                </option>
                                <option value="customer" {{ old('type') == 'customer' ? 'selected' : '' }}>Customer
                                </option>
                                <option value="client" {{ old('type') == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="prospect" {{ old('type') == 'prospect' ? 'selected' : '' }}>Prospect
                                </option>
                                <option value="partner" {{ old('type') == 'partner' ? 'selected' : '' }}>Partner</option>
                                <option value="contractor" {{ old('type') == 'contractor' ? 'selected' : '' }}>Contractor
                                </option>
                                <option value="government" {{ old('type') == 'government' ? 'selected' : '' }}>Government
                                </option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="personal_address" class="form-label">Alamat Pribadi</label>
                        <textarea name="personal_address" id="personal_address" class="form-control" rows="2">{{ old('personal_address') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="work_address" class="form-label">Alamat Kantor</label>
                        <textarea name="work_address" id="work_address" class="form-control" rows="2">{{ old('work_address') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
