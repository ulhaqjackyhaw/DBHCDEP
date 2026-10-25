@extends('layouts.app')

@section('title', 'Tambah Data PGS')
@section('header-title', 'Tambah Data PGS Baru')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('data-pgs.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik"
                            name="nik" value="{{ old('nik') }}" required>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                            name="nama" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jabatan_definitif" class="form-label">Jabatan Definitif</label>
                        <input type="text" class="form-control @error('jabatan_definitif') is-invalid @enderror"
                            id="jabatan_definitif" name="jabatan_definitif" value="{{ old('jabatan_definitif') }}" required>
                        @error('jabatan_definitif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jabatan_pgs" class="form-label">Jabatan PGS</label>
                        <input type="text" class="form-control @error('jabatan_pgs') is-invalid @enderror"
                            id="jabatan_pgs" name="jabatan_pgs" value="{{ old('jabatan_pgs') }}" required>
                        @error('jabatan_pgs')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lokasi_unit_kerja" class="form-label">Lokasi/Unit Kerja</label>
                        <input type="text" class="form-control @error('lokasi_unit_kerja') is-invalid @enderror"
                            id="lokasi_unit_kerja" name="lokasi_unit_kerja" value="{{ old('lokasi_unit_kerja') }}" required>
                        @error('lokasi_unit_kerja')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pgs" class="form-label">Tanggal PGS</label>
                        <input type="date" class="form-control @error('tanggal_pgs') is-invalid @enderror"
                            id="tanggal_pgs" name="tanggal_pgs" value="{{ old('tanggal_pgs') }}" required>
                        @error('tanggal_pgs')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Tanggal mulai menjabat sebagai PGS</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_selesai_pgs" class="form-label">Tanggal Selesai PGS <span
                                class="text-muted">(Opsional)</span></label>
                        <input type="date" class="form-control @error('tanggal_selesai_pgs') is-invalid @enderror"
                            id="tanggal_selesai_pgs" name="tanggal_selesai_pgs" value="{{ old('tanggal_selesai_pgs') }}">
                        @error('tanggal_selesai_pgs')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Tanggal target selesai PGS (akan ada notifikasi 15 hari sebelumnya)</div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('data-pgs.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
