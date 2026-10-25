@extends('layouts.app')

@section('title', 'Edit Data Penugasan')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Data Penugasan
                    </h2>
                    <a href="{{ route('data-penugasan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('data-penugasan.update', $dataPenugasan->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Data Karyawan Section -->
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-user me-2 text-primary"></i>Data Karyawan
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nik" class="form-label">NIK <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nik') is-invalid @enderror"
                                            id="nik" name="nik" value="{{ old('nik', $dataPenugasan->nik) }}"
                                            required>
                                        @error('nik')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nama" class="form-label">Nama <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                            id="nama" name="nama" value="{{ old('nama', $dataPenugasan->nama) }}"
                                            required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="kj" class="form-label">KJ (Kelompok Jabatan)</label>
                                        <input type="text" class="form-control @error('kj') is-invalid @enderror"
                                            id="kj" name="kj" value="{{ old('kj', $dataPenugasan->kj) }}">
                                        @error('kj')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="pic" class="form-label">PIC (Person in Charge)</label>
                                        <input type="text" class="form-control @error('pic') is-invalid @enderror"
                                            id="pic" name="pic" value="{{ old('pic', $dataPenugasan->pic) }}">
                                        @error('pic')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Posisi Definitif Section -->
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-briefcase me-2 text-success"></i>Posisi Definitif
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="jabatan_definitif" class="form-label">Jabatan Definitif</label>
                                        <input type="text"
                                            class="form-control @error('jabatan_definitif') is-invalid @enderror"
                                            id="jabatan_definitif" name="jabatan_definitif"
                                            value="{{ old('jabatan_definitif', $dataPenugasan->jabatan_definitif) }}">
                                        @error('jabatan_definitif')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="unit_definitif" class="form-label">Unit Definitif</label>
                                        <input type="text"
                                            class="form-control @error('unit_definitif') is-invalid @enderror"
                                            id="unit_definitif" name="unit_definitif"
                                            value="{{ old('unit_definitif', $dataPenugasan->unit_definitif) }}">
                                        @error('unit_definitif')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="lokasi_definitif" class="form-label">Lokasi Definitif</label>
                                        <input type="text"
                                            class="form-control @error('lokasi_definitif') is-invalid @enderror"
                                            id="lokasi_definitif" name="lokasi_definitif"
                                            value="{{ old('lokasi_definitif', $dataPenugasan->lokasi_definitif) }}">
                                        @error('lokasi_definitif')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Data Penugasan Section -->
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-exchange-alt me-2 text-info"></i>Data Penugasan
                                </h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="unit_penugasan" class="form-label">Unit Penugasan</label>
                                        <input type="text"
                                            class="form-control @error('unit_penugasan') is-invalid @enderror"
                                            id="unit_penugasan" name="unit_penugasan"
                                            value="{{ old('unit_penugasan', $dataPenugasan->unit_penugasan) }}">
                                        @error('unit_penugasan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="lokasi_penugasan" class="form-label">Lokasi Penugasan</label>
                                        <input type="text"
                                            class="form-control @error('lokasi_penugasan') is-invalid @enderror"
                                            id="lokasi_penugasan" name="lokasi_penugasan"
                                            value="{{ old('lokasi_penugasan', $dataPenugasan->lokasi_penugasan) }}">
                                        @error('lokasi_penugasan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="nomor_sprint" class="form-label">Nomor Sprint/Surat</label>
                                        <input type="text"
                                            class="form-control @error('nomor_sprint') is-invalid @enderror"
                                            id="nomor_sprint" name="nomor_sprint"
                                            value="{{ old('nomor_sprint', $dataPenugasan->nomor_sprint) }}"
                                            placeholder="Contoh: SPR.CGR.CEO.196/KP.04.01/2025">
                                        @error('nomor_sprint')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                        <input type="date"
                                            class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                            id="tanggal_mulai" name="tanggal_mulai"
                                            value="{{ old('tanggal_mulai', $dataPenugasan->tanggal_mulai ? \Carbon\Carbon::createFromFormat('d/m/Y', $dataPenugasan->tanggal_mulai)->format('Y-m-d') : '') }}">
                                        @error('tanggal_mulai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                        <input type="date"
                                            class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                            id="tanggal_selesai" name="tanggal_selesai"
                                            value="{{ old('tanggal_selesai', $dataPenugasan->tanggal_selesai ? \Carbon\Carbon::createFromFormat('d/m/Y', $dataPenugasan->tanggal_selesai)->format('Y-m-d') : '') }}">
                                        @error('tanggal_selesai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                            rows="3">{{ old('keterangan', $dataPenugasan->keterangan) }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <a href="{{ route('data-penugasan.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
