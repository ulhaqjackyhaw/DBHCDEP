@extends('layouts.app')

@section('title', 'Detail Realisasi')
@section('header-title', 'Detail Realisasi ' . ($realisasi->tahun ?? date('Y')))

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5">
        <dl class="row mb-0">
            <dt class="col-sm-3">Program Kerja</dt>
            <dd class="col-sm-9">{{ $realisasi->program_kerja }}</dd>

            <dt class="col-sm-3">RKAP {{ $realisasi->tahun }}</dt>
            <dd class="col-sm-9">{{ number_format($realisasi->rkap, 2, ',', '.') }}</dd>

            <dt class="col-sm-3">Realisasi Jan–Mar {{ $realisasi->tahun }}</dt>
            <dd class="col-sm-9">{{ number_format($realisasi->realisasi_jan_mar, 2, ',', '.') }}</dd>

            <dt class="col-sm-3">Realisasi Jan–Jun {{ $realisasi->tahun }}</dt>
            <dd class="col-sm-9">{{ number_format($realisasi->realisasi_jan_jun, 2, ',', '.') }}</dd>

            <dt class="col-sm-3">Realisasi Jul–Sep {{ $realisasi->tahun }}</dt>
            <dd class="col-sm-9">{{ number_format($realisasi->realisasi_jul_sep, 2, ',', '.') }}</dd>

            <dt class="col-sm-3">Realisasi Jul–Des {{ $realisasi->tahun }}</dt>
            <dd class="col-sm-9">{{ number_format($realisasi->realisasi_jul_des, 2, ',', '.') }}</dd>

            <dt class="col-sm-3">Achievement Semester 1</dt>
            <dd class="col-sm-9">{{ number_format(($realisasi->ach_s1 ?? 0) * 100, 2, ',', '.') }}%</dd>

            <dt class="col-sm-3">Achievement Semester 2</dt>
            <dd class="col-sm-9">{{ number_format(($realisasi->ach_s2 ?? 0) * 100, 2, ',', '.') }}%</dd>

            <dt class="col-sm-3">Achievement {{ $realisasi->tahun }}</dt>
            <dd class="col-sm-9">{{ number_format(($realisasi->ach_year ?? 0) * 100, 2, ',', '.') }}%</dd>
        </dl>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('realisasi.index') }}" class="btn btn-light">Kembali</a>
            @can('admin')
                <a href="{{ route('realisasi.edit', $realisasi) }}" class="btn btn-primary">Edit</a>
            @endcan
        </div>
    </div>
@endsection
