@extends('layouts.app')

@section('title', 'Edit Realisasi')
@section('header-title', 'Edit Realisasi ' . ($realisasi->tahun ?? date('Y')))

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('realisasi.update', $realisasi) }}" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-4">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun"
                                    value="{{ old('tahun', $realisasi->tahun ?? date('Y')) }}" min="2000" max="2100"
                                    class="form-control @error('tahun') is-invalid @enderror" required>
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Program Kerja</label>
                                <input type="text" name="program_kerja"
                                    value="{{ old('program_kerja', $realisasi->program_kerja) }}"
                                    class="form-control @error('program_kerja') is-invalid @enderror" required>
                                @error('program_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <label class="form-label">RKAP</label>
                                <input type="number" step="0.01" name="rkap"
                                    value="{{ old('rkap', $realisasi->rkap) }}"
                                    class="form-control @error('rkap') is-invalid @enderror" required>
                                @error('rkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <label class="form-label">Realisasi Jan–Mar</label>
                                <input type="number" step="0.01" name="realisasi_jan_mar"
                                    value="{{ old('realisasi_jan_mar', $realisasi->realisasi_jan_mar) }}"
                                    class="form-control @error('realisasi_jan_mar') is-invalid @enderror">
                                @error('realisasi_jan_mar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <label class="form-label">Realisasi Jan–Jun</label>
                                <input type="number" step="0.01" name="realisasi_jan_jun"
                                    value="{{ old('realisasi_jan_jun', $realisasi->realisasi_jan_jun) }}"
                                    class="form-control @error('realisasi_jan_jun') is-invalid @enderror">
                                @error('realisasi_jan_jun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <label class="form-label">Realisasi Jul–Sep</label>
                                <input type="number" step="0.01" name="realisasi_jul_sep"
                                    value="{{ old('realisasi_jul_sep', $realisasi->realisasi_jul_sep) }}"
                                    class="form-control @error('realisasi_jul_sep') is-invalid @enderror">
                                @error('realisasi_jul_sep')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <label class="form-label">Realisasi Jul–Des</label>
                                <input type="number" step="0.01" name="realisasi_jul_des"
                                    value="{{ old('realisasi_jul_des', $realisasi->realisasi_jul_des) }}"
                                    class="form-control @error('realisasi_jul_des') is-invalid @enderror">
                                @error('realisasi_jul_des')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <label class="form-label">Total</label>
                                <input type="number" step="0.01" name="total"
                                    value="{{ old('total', $realisasi->total) }}"
                                    class="form-control @error('total') is-invalid @enderror">
                                @error('total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('realisasi.index') }}" class="btn btn-light">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
