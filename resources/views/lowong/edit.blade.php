@extends('layouts.app')

@section('title', 'Edit Jabatan Lowong')
@section('header-title', 'Edit Jabatan Lowong')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-4">Formulir Edit Jabatan Lowong</h6>

                    <form action="{{ route('lowong.update', $lowong->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jabatan" class="form-label">Nama Jabatan</label>
                                <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ old('jabatan', $lowong->jabatan) }}" required>
                                @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $lowong->nama) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" name="status" value="{{ old('status', $lowong->status) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nik" class="form-label">NIK / Lowong</label>
                                <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik', $lowong->nik) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lokasi" class="form-label">Lokasi</label>
                                <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ old('lokasi', $lowong->lokasi) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Level</label>
                                <input type="text" class="form-control" id="level" name="level" value="{{ old('level', $lowong->level) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipe" class="form-label">Tipe (S/F)</label>
                                <input type="text" class="form-control" id="tipe" name="tipe" value="{{ old('tipe', $lowong->tipe) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kj_api" class="form-label">KJ API</label>
                                <input type="text" class="form-control" id="kj_api" name="kj_api" value="{{ old('kj_api', $lowong->kj_api) }}">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('lowong.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection