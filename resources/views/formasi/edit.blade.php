@extends('layouts.app')

@section('title', 'Edit Data Formasi')
@section('header-title', 'Edit Data Formasi')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('formasi.update', $formasi->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode_jabatan" class="form-label">Kode Jabatan</label>
                        <input type="text" class="form-control @error('kode_jabatan') is-invalid @enderror"
                            id="kode_jabatan" name="kode_jabatan" value="{{ old('kode_jabatan', $formasi->kode_jabatan) }}"
                            required>
                        @error('kode_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="unit_deputy_egm" class="form-label">Unit Deputy EGM</label>
                        <input type="text" class="form-control @error('unit_deputy_egm') is-invalid @enderror"
                            id="unit_deputy_egm" name="unit_deputy_egm"
                            value="{{ old('unit_deputy_egm', $formasi->unit_deputy_egm) }}">
                        @error('unit_deputy_egm')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="unit_assistant_deputy" class="form-label">Unit Assistant Deputy</label>
                        <input type="text" class="form-control @error('unit_assistant_deputy') is-invalid @enderror"
                            id="unit_assistant_deputy" name="unit_assistant_deputy"
                            value="{{ old('unit_assistant_deputy', $formasi->unit_assistant_deputy) }}">
                        @error('unit_assistant_deputy')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="unit_division_head" class="form-label">Unit Division Head</label>
                        <input type="text" class="form-control @error('unit_division_head') is-invalid @enderror"
                            id="unit_division_head" name="unit_division_head"
                            value="{{ old('unit_division_head', $formasi->unit_division_head) }}">
                        @error('unit_division_head')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="unit_department_head" class="form-label">Unit Department Head</label>
                        <input type="text" class="form-control @error('unit_department_head') is-invalid @enderror"
                            id="unit_department_head" name="unit_department_head"
                            value="{{ old('unit_department_head', $formasi->unit_department_head) }}">
                        @error('unit_department_head')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lokasi_kerja" class="form-label">Lokasi Kerja</label>
                        <input type="text" class="form-control @error('lokasi_kerja') is-invalid @enderror"
                            id="lokasi_kerja" name="lokasi_kerja" value="{{ old('lokasi_kerja', $formasi->lokasi_kerja) }}"
                            required>
                        @error('lokasi_kerja')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="unit_kerja" class="form-label">Unit Kerja</label>
                        <input type="text" class="form-control @error('unit_kerja') is-invalid @enderror" id="unit_kerja"
                            name="unit_kerja" value="{{ old('unit_kerja', $formasi->unit_kerja) }}" required>
                        @error('unit_kerja')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan"
                            name="jabatan" value="{{ old('jabatan', $formasi->jabatan) }}" required>
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kelompok_kelas_jabatan" class="form-label">Kelompok Kelas Jabatan</label>
                        <input type="text" class="form-control @error('kelompok_kelas_jabatan') is-invalid @enderror"
                            id="kelompok_kelas_jabatan" name="kelompok_kelas_jabatan"
                            value="{{ old('kelompok_kelas_jabatan', $formasi->kelompok_kelas_jabatan) }}" required>
                        @error('kelompok_kelas_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="grade" class="form-label">Grade</label>
                        <input type="text" class="form-control @error('grade') is-invalid @enderror" id="grade"
                            name="grade" value="{{ old('grade', $formasi->grade) }}" required>
                        @error('grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kuota" class="form-label">Kuota</label>
                        <input type="number" min="1" class="form-control @error('kuota') is-invalid @enderror"
                            id="kuota" name="kuota" value="{{ old('kuota', $formasi->kuota) }}" required>
                        @error('kuota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('formasi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
