@extends('template.master')

@section('users-active', 'active')

@section('content')
    <div class="container-fluid py-0 px-0">
        <h1 class="h3 mb-3"><strong>Tambah User</strong></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    {{-- Role --}}
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select name="role_id" id="role_id" class="form-select select2" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach ($roles as $id => $name)
                                <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                            required autofocus>
                    </div>

                    {{-- Username --}}
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control"
                            value="{{ old('username') }}" required>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                            required>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status</label>
                        <select name="is_active" id="is_active" class="form-select select2">
                            <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
