@extends('template.master')

@section('contacts-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Edit Kontak</strong></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('contacts.update', $contact->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="prefix" class="form-label">Sapaan</label>
                        <input type="text" name="prefix" id="prefix"
                            class="form-control @error('prefix') is-invalid @enderror"
                            value="{{ old('prefix', $contact->prefix) }}" required>
                        @error('prefix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $contact->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="organization_name" class="form-label">Nama Organisasi</label>
                        <input type="text" name="organization_name" id="organization_name" class="form-control"
                            value="{{ old('organization_name', $contact->organization_name) }}">
                    </div>

                    <div class="mb-3">
                        <label for="position" class="form-label">Jabatan</label>
                        <input type="text" name="position" id="position" class="form-control"
                            value="{{ old('position', $contact->position) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email', $contact->email) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="phone" class="form-label">Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                value="{{ old('phone', $contact->phone) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="whatsapp" class="form-label">WhatsApp</label>
                            <input type="text" name="whatsapp" id="whatsapp" class="form-control"
                                value="{{ old('whatsapp', $contact->whatsapp) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="npwp" class="form-label">NPWP</label>
                        <input type="text" name="npwp" id="npwp" class="form-control"
                            value="{{ old('npwp', $contact->npwp) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="birthday" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birthday" id="birthday" class="form-control"
                                value="{{ old('birthday', $contact->birthday) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Tipe</label>
                            <select name="type" id="type"
                                class="form-select select2 @error('type') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                @php $types = ['supplier','customer','client','prospect','partner','contractor','government','other']; @endphp
                                @foreach ($types as $t)
                                    <option value="{{ $t }}"
                                        {{ old('type', $contact->type) == $t ? 'selected' : '' }}>
                                        {{ ucfirst($t) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="personal_address" class="form-label">Alamat Pribadi</label>
                        <textarea name="personal_address" id="personal_address" class="form-control" rows="2">{{ old('personal_address', $contact->personal_address) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="work_address" class="form-label">Alamat Kantor</label>
                        <textarea name="work_address" id="work_address" class="form-control" rows="2">{{ old('work_address', $contact->work_address) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
