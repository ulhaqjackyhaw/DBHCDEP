@extends('layouts.app')

@section('title', 'Detail Data Formasi')
@section('header-title', 'Detail Data Formasi')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Kode Jabatan</label>
                    <p class="form-control-plaintext">{{ $formasi->kode_jabatan }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Unit Deputy EGM</label>
                    <p class="form-control-plaintext">{{ $formasi->unit_deputy_egm ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Unit Assistant Deputy</label>
                    <p class="form-control-plaintext">{{ $formasi->unit_assistant_deputy ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Unit Division Head</label>
                    <p class="form-control-plaintext">{{ $formasi->unit_division_head ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Unit Department Head</label>
                    <p class="form-control-plaintext">{{ $formasi->unit_department_head ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Lokasi Kerja</label>
                    <p class="form-control-plaintext">{{ $formasi->lokasi_kerja }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Unit Kerja</label>
                    <p class="form-control-plaintext">{{ $formasi->unit_kerja }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Jabatan</label>
                    <p class="form-control-plaintext">{{ $formasi->jabatan }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Kelompok Kelas Jabatan</label>
                    <p class="form-control-plaintext">{{ $formasi->kelompok_kelas_jabatan }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Grade</label>
                    <p class="form-control-plaintext">{{ $formasi->grade }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Kuota</label>
                    <p class="form-control-plaintext">{{ $formasi->kuota }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Dibuat</label>
                    <p class="form-control-plaintext">{{ $formasi->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Diperbarui</label>
                    <p class="form-control-plaintext">{{ $formasi->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('formasi.index') }}" class="btn btn-outline-secondary">Kembali</a>
                @can('admin')
                    <a href="{{ route('formasi.edit', $formasi) }}" class="btn btn-primary">Edit</a>
                @endcan
            </div>
        </div>
    </div>
@endsection
