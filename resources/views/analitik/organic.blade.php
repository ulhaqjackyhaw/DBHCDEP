@extends('layouts.app')

@section('title', 'Analitik Karyawan Organik')
@section('header-title', 'Analitik Karyawan Organik')

@push('head-scripts')
    {{-- Dependensi & CSS --}}
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js">
    </script>

    <style>
        :root {
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-color-dark: #1e293b;
            --text-color-light: #64748b;
            --border-color: #e2e8f0;
            --primary-color: #4f46e5;
        }

        body {
            background-color: var(--body-bg);
            font-family: 'Inter', sans-serif;
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color-dark);
        }

        .chart-container {
            position: relative;
        }

        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
        }

        .nav-tabs .nav-link {
            border-width: 0;
            border-bottom: 2px solid transparent;
            color: var(--text-color-light);
            font-weight: 600;
            padding: 0.75rem 1.25rem;
        }

        .nav-tabs .nav-link.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
            background: none;
        }

        .tab-content {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-top: 0;
            padding: 2rem;
            border-radius: 0 0 1rem 1rem;
        }


        .table-matrix td {
            text-align: center;
            font-weight: 600;
            padding: 0.75rem;
        }


        .table-matrix .grade-label,
        .cell-name {
            text-align: left;
        }

        .cell-role {
            font-size: .85rem;
            color: #64748b;
            text-align: left;
        }

        .table thead th {
            background-color: #f1f5f9;
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            padding: 0.4rem 0.75rem;
        }

        .quadrant-legend {
            list-style: none;
            padding-left: 0;
        }

        .quadrant-legend li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .quadrant-legend .icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 0.5rem;
            margin-right: 1rem;
            font-size: 1.2rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card mb-4">
            <div class="d-flex flex-column align-items-center justify-content-center p-3">
                <div style="background-color: #e0e7ff; color: #4338ca; width: 64px; height: 64px; border-radius: 50%; display: grid; place-items: center; font-size: 2rem; flex-shrink: 0;"
                    class="mb-2"><i class="fa-solid fa-users"></i></div>
                <div class="text-center">
                    <div style="font-size: 2.5rem; font-weight: 700;">{{ number_format($totalOrganic ?? 0) }}</div>
                    <div style="font-size: 1rem; color: var(--text-color-light);">Total Karyawan Organik</div>
                </div>

                {{-- Breakdown Status Jabatan --}}
                @if (isset($statusJabatanCounts) && $statusJabatanCounts->count() > 0)
                    <div class="mt-4 pt-3 border-top w-100">
                        <div class="row g-3">
                            @foreach ($statusJabatanCounts as $status)
                                @php
                                    $percentage = $totalOrganic > 0 ? ($status->total / $totalOrganic) * 100 : 0;
                                    $iconClass =
                                        strtoupper($status->status_jabatan) === 'PEJABAT' ? 'fa-user-tie' : 'fa-user';
                                    $colorClass =
                                        strtoupper($status->status_jabatan) === 'PEJABAT'
                                            ? 'text-success'
                                            : 'text-info';
                                    $bgColor =
                                        strtoupper($status->status_jabatan) === 'PEJABAT' ? '#dcfce7' : '#dbeafe';
                                    $textColor =
                                        strtoupper($status->status_jabatan) === 'PEJABAT' ? '#166534' : '#1e40af';
                                @endphp
                                <div class="col-6">
                                    <div class="d-flex align-items-center justify-content-center flex-column p-3 rounded"
                                        style="background-color: {{ $bgColor }};">
                                        <i class="fa-solid {{ $iconClass }} {{ $colorClass }} mb-2"
                                            style="font-size: 1.5rem;"></i>
                                        <div class="fw-bold" style="font-size: 1.75rem; color: {{ $textColor }};">
                                            {{ number_format($status->total) }}
                                        </div>
                                        <div class="small" style="color: {{ $textColor }}; opacity: 0.8;">
                                            {{ $status->status_jabatan }}
                                        </div>
                                        <div class="small text-muted mt-1">
                                            ({{ number_format($percentage, 1) }}%)
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <ul class="nav nav-tabs" id="analyticsTab" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#dashboard" type="button">📊 Anilis 1</button></li>
            {{-- <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#matriks"
                    type="button">🧬 Analisis 1</button></li> --}}
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#lanjutan" type="button">📖 Analisis 2</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#unit"
                    type="button">🏢 Analisis 3</button></li>
        </ul>

        <div class="tab-content" id="analyticsTabContent">
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                {{-- Konten Dashboard tidak berubah --}}
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Piramida Jabatan per Lokasi</h5>
                                <div class="d-flex flex-row flex-wrap align-items-start">
                                    <div class="chart-container flex-grow-1" style="height:400px; min-width:0;">
                                        <canvas id="kkjLocationChart"></canvas>
                                        <div class="small text-muted mt-2 text-center w-100">
                                            <i class="fa fa-info-circle text-info"></i>
                                            Klik pada Kotak warna BOD- di bawah chart untuk menyembunyikan/menampilkan jenis
                                            jabatan tertentu.
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <br>
                                @if (isset($kkjLocationLabels) && isset($kkjLocationLabels[0]) && isset($kkjLocationDatasets))
                                    <div class="mt-4" style="max-width:100%; overflow-x:auto;">
                                        <div class="fw-bold mb-2">Total & Rincian per Lokasi</div>
                                        <table class="table table-sm table-bordered mb-0 align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-nowrap">Lokasi</th>
                                                    @foreach ($kkjLocationDatasets as $ds)
                                                        <th class="text-end">{{ $ds['label'] }}</th>
                                                    @endforeach
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($kkjLocationLabels as $i => $lokasi)
                                                    <tr>
                                                        <td class="text-nowrap">{{ $lokasi }}</td>
                                                        @php $rowTotal = 0; @endphp
                                                        @foreach ($kkjLocationDatasets as $ds)
                                                            <td class="text-end">{{ number_format($ds['data'][$i] ?? 0) }}
                                                            </td>
                                                            @php $rowTotal += $ds['data'][$i] ?? 0; @endphp
                                                        @endforeach
                                                        <td class="text-end fw-bold">{{ number_format($rowTotal) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Card Jumlah Karyawan Organik per Lokasi --}}
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title">👥 Jumlah Karyawan Organik per Lokasi</h5>
                            <p class="text-muted small mb-3">
                                <strong>Distribusi jumlah karyawan organik</strong> berdasarkan lokasi geografis.<br>
                                <i class="fa fa-info-circle text-info"></i> <strong>Data:</strong> Total karyawan organik •
                                Persentase dari total • Status kepegawaian organik
                            </p>

                            <div class="row">
                                @if (isset($locationSummary) && $locationSummary->count() > 0)
                                    @foreach ($locationSummary as $location)
                                        <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body text-center">
                                                    <div class="display-6 text-primary mb-2">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </div>
                                                    <h6 class="card-title fw-bold text-truncate"
                                                        title="{{ $location['location'] }}">
                                                        {{ $location['location'] }}
                                                    </h6>
                                                    <div class="display-4 fw-bold text-dark mb-2">
                                                        {{ number_format($location['total_employees']) }}
                                                    </div>
                                                    <p class="text-muted mb-3">karyawan organik</p>

                                                    {{-- Progress bar untuk proporsi relatif --}}
                                                    @if (isset($totalOrganic) && $totalOrganic > 0)
                                                        <div class="mt-3">
                                                            @php
                                                                $percentage =
                                                                    ($location['total_employees'] / $totalOrganic) *
                                                                    100;
                                                            @endphp
                                                            <div class="small text-muted mb-1">
                                                                {{ number_format($percentage, 1) }}% dari total organik
                                                            </div>
                                                            <div class="progress" style="height: 8px;">
                                                                <div class="progress-bar bg-primary"
                                                                    style="width: {{ $percentage }}%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                            <h6 class="text-muted">Data lokasi tidak tersedia</h6>
                                            <p class="text-muted small">Belum ada data karyawan dengan informasi lokasi
                                                yang lengkap</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Summary Statistics --}}
                            @if (isset($locationSummary) && $locationSummary->count() > 0)
                                <div class="mt-4 p-3 bg-light rounded-3">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <div class="small text-muted">Total Lokasi</div>
                                            <div class="h5 fw-bold text-primary mb-0">{{ $locationSummary->count() }}
                                                lokasi
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small text-muted">Lokasi Terbesar</div>
                                            <div class="h6 fw-bold text-success mb-0">
                                                @php
                                                    $largestLocation = $locationSummary
                                                        ->sortByDesc('total_employees')
                                                        ->first();
                                                @endphp
                                                {{ $largestLocation['location'] }}<br>
                                                <small
                                                    class="text-muted">({{ number_format($largestLocation['total_employees']) }}
                                                    karyawan)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small text-muted">Rata-rata per Lokasi</div>
                                            <div class="h5 fw-bold text-info mb-0">
                                                {{ number_format($locationSummary->avg('total_employees'), 0) }} karyawan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12" id="bodNamesSection">
                        <div class="card">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <h6 class="card-title mb-0">Daftar Nama Karyawan berdasarkan Kelompokan Kelas Jabatan
                                    </h6>
                                    <div class="input-group input-group-sm" style="max-width: 260px;">
                                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                                        <input type="text" class="form-control" id="bodSearchInput"
                                            placeholder="Cari nama atau jabatan..." autocomplete="off"
                                            @if (
                                                ($bodGroups['BOD-1'] ?? []) === [] &&
                                                    ($bodGroups['BOD-2'] ?? []) === [] &&
                                                    ($bodGroups['BOD-3'] ?? []) === [] &&
                                                    ($bodGroups['BOD-4'] ?? []) === []
                                            ) disabled @endif>
                                    </div>
                                </div>
                                <div style="max-height: 400px; overflow-y: auto;">
                                    <div class="table-responsive">
                                        @php
                                            $bodGroups = $bodGroups ?? [];
                                            $maxRows = max(
                                                count($bodGroups['BOD-1'] ?? []),
                                                count($bodGroups['BOD-2'] ?? []),
                                                count($bodGroups['BOD-3'] ?? []),
                                                count($bodGroups['BOD-4'] ?? []),
                                            );
                                        @endphp
                                        <table class="table table-sm table-fixed align-top">
                                            <thead>
                                                <tr>
                                                    <th style="width:25%">BOD-1</th>
                                                    <th style="width:25%">BOD-2</th>
                                                    <th style="width:25%">BOD-3</th>
                                                    <th style="width:25%">BOD-4</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < $maxRows; $i++)
                                                    <tr data-bod-row>
                                                        <td>
                                                            @if (isset($bodGroups['BOD-1'][$i]))
                                                                <div class="cell-name">
                                                                    {{ $bodGroups['BOD-1'][$i]['nama'] }}
                                                                </div>
                                                                <div class="cell-role">
                                                                    {{ $bodGroups['BOD-1'][$i]['jabatan'] }}</div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (isset($bodGroups['BOD-2'][$i]))
                                                                <div class="cell-name">
                                                                    {{ $bodGroups['BOD-2'][$i]['nama'] }}
                                                                </div>
                                                                <div class="cell-role">
                                                                    {{ $bodGroups['BOD-2'][$i]['jabatan'] }}</div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (isset($bodGroups['BOD-3'][$i]))
                                                                <div class="cell-name">
                                                                    {{ $bodGroups['BOD-3'][$i]['nama'] }}
                                                                </div>
                                                                <div class="cell-role">
                                                                    {{ $bodGroups['BOD-3'][$i]['jabatan'] }}</div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (isset($bodGroups['BOD-4'][$i]))
                                                                <div class="cell-name">
                                                                    {{ $bodGroups['BOD-4'][$i]['nama'] }}
                                                                </div>
                                                                <div class="cell-role">
                                                                    {{ $bodGroups['BOD-4'][$i]['jabatan'] }}</div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endfor
                                                <tr id="bodSearchEmptyRow" class="d-none">
                                                    <td colspan="4" class="text-center text-muted p-4">Tidak ada nama
                                                        yang cocok.</td>
                                                </tr>
                                                @if ($maxRows === 0)
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted p-4">Data BOD
                                                            tidak
                                                            tersedia.</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Tabel Gender per Lokasi --}}
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fa-solid fa-venus-mars me-2 text-primary"></i>
                                    Distribusi Gender per Lokasi
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50%;">Lokasi</th>
                                                <th class="text-center" style="width: 20%;">Laki-laki</th>
                                                <th class="text-center" style="width: 20%;">Perempuan</th>
                                                <th class="text-center" style="width: 10%;">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                // Gunakan data dari chart yang sudah ada
                                                $genderData = [];
                                                if (isset($genderLocationLabels) && isset($genderLocationDatasets)) {
                                                    foreach ($genderLocationLabels as $index => $lokasi) {
                                                        $genderData[$lokasi] = [
                                                            'Laki-laki' => 0,
                                                            'Perempuan' => 0,
                                                        ];
                                                        foreach ($genderLocationDatasets as $dataset) {
                                                            if ($dataset['label'] === 'Laki-laki') {
                                                                $genderData[$lokasi]['Laki-laki'] =
                                                                    $dataset['data'][$index] ?? 0;
                                                            } elseif ($dataset['label'] === 'Perempuan') {
                                                                $genderData[$lokasi]['Perempuan'] =
                                                                    $dataset['data'][$index] ?? 0;
                                                            }
                                                        }
                                                    }
                                                }
                                                $totalLaki = 0;
                                                $totalPerempuan = 0;
                                            @endphp
                                            @foreach ($genderData as $lokasi => $data)
                                                @php
                                                    $totalLokasi = $data['Laki-laki'] + $data['Perempuan'];
                                                    $totalLaki += $data['Laki-laki'];
                                                    $totalPerempuan += $data['Perempuan'];
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $lokasi }}</strong></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary"
                                                            style="min-width: 50px;">{{ number_format($data['Laki-laki']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-danger"
                                                            style="min-width: 50px;">{{ number_format($data['Perempuan']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ number_format($totalLokasi) }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-secondary fw-bold">
                                                <td>TOTAL</td>
                                                <td class="text-center">{{ number_format($totalLaki) }}</td>
                                                <td class="text-center">{{ number_format($totalPerempuan) }}</td>
                                                <td class="text-center">{{ number_format($totalLaki + $totalPerempuan) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Generasi per Lokasi --}}
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fa-solid fa-users-between-lines me-2 text-success"></i>
                                    Distribusi Generasi per Lokasi
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40%;">Lokasi</th>
                                                <th class="text-center" style="width: 15%;">Gen Z</th>
                                                <th class="text-center" style="width: 15%;">Milenial</th>
                                                <th class="text-center" style="width: 15%;">Gen X</th>
                                                <th class="text-center" style="width: 15%;">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                // Gunakan data dari chart yang sudah ada
                                                $generasiData = [];
                                                if (isset($ageLocationLabels) && isset($ageLocationDatasets)) {
                                                    foreach ($ageLocationLabels as $index => $lokasi) {
                                                        $generasiData[$lokasi] = [
                                                            'Gen Z' => 0,
                                                            'Milenial' => 0,
                                                            'Gen X' => 0,
                                                        ];
                                                        foreach ($ageLocationDatasets as $dataset) {
                                                            $label = $dataset['label'];
                                                            if (isset($generasiData[$lokasi][$label])) {
                                                                $generasiData[$lokasi][$label] =
                                                                    $dataset['data'][$index] ?? 0;
                                                            }
                                                        }
                                                    }
                                                }
                                                $totalGenZ = 0;
                                                $totalMilenial = 0;
                                                $totalGenX = 0;
                                            @endphp
                                            @foreach ($generasiData as $lokasi => $data)
                                                @php
                                                    $totalLokasi = $data['Gen Z'] + $data['Milenial'] + $data['Gen X'];
                                                    $totalGenZ += $data['Gen Z'];
                                                    $totalMilenial += $data['Milenial'];
                                                    $totalGenX += $data['Gen X'];
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $lokasi }}</strong></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info"
                                                            style="min-width: 40px;">{{ number_format($data['Gen Z']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success"
                                                            style="min-width: 40px;">{{ number_format($data['Milenial']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning"
                                                            style="min-width: 40px;">{{ number_format($data['Gen X']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ number_format($totalLokasi) }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-secondary fw-bold">
                                                <td>TOTAL</td>
                                                <td class="text-center">{{ number_format($totalGenZ) }}</td>
                                                <td class="text-center">{{ number_format($totalMilenial) }}</td>
                                                <td class="text-center">{{ number_format($totalGenX) }}</td>
                                                <td class="text-center">
                                                    {{ number_format($totalGenZ + $totalMilenial + $totalGenX) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Masa Kerja per Lokasi --}}
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fa-solid fa-business-time me-2 text-warning"></i>
                                    Distribusi Masa Kerja per Lokasi
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 30%;">Lokasi</th>
                                                <th class="text-center" style="width: 12%;">0-1 thn</th>
                                                <th class="text-center" style="width: 12%;">2-3 thn</th>
                                                <th class="text-center" style="width: 12%;">4-6 thn</th>
                                                <th class="text-center" style="width: 12%;">7-10 thn</th>
                                                <th class="text-center" style="width: 12%;">&gt;10 thn</th>
                                                <th class="text-center" style="width: 10%;">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                // Gunakan data dari chart yang sudah ada
                                                $masaKerjaData = [];
                                                if (isset($tenureLocationLabels) && isset($tenureLocationDatasets)) {
                                                    foreach ($tenureLocationLabels as $index => $lokasi) {
                                                        $masaKerjaData[$lokasi] = [
                                                            '0-1 thn' => 0,
                                                            '2-3 thn' => 0,
                                                            '4-6 thn' => 0,
                                                            '7-10 thn' => 0,
                                                            '>10 thn' => 0,
                                                        ];
                                                        foreach ($tenureLocationDatasets as $dataset) {
                                                            $label = $dataset['label'];
                                                            if (isset($masaKerjaData[$lokasi][$label])) {
                                                                $masaKerjaData[$lokasi][$label] =
                                                                    $dataset['data'][$index] ?? 0;
                                                            }
                                                        }
                                                    }
                                                }
                                                $total01 = 0;
                                                $total23 = 0;
                                                $total46 = 0;
                                                $total710 = 0;
                                                $totalAbove10 = 0;
                                            @endphp
                                            @foreach ($masaKerjaData as $lokasi => $data)
                                                @php
                                                    $totalLokasi = array_sum($data);
                                                    $total01 += $data['0-1 thn'];
                                                    $total23 += $data['2-3 thn'];
                                                    $total46 += $data['4-6 thn'];
                                                    $total710 += $data['7-10 thn'];
                                                    $totalAbove10 += $data['>10 thn'];
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $lokasi }}</strong></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info"
                                                            style="min-width: 45px;">{{ number_format($data['0-1 thn']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success"
                                                            style="min-width: 45px;">{{ number_format($data['2-3 thn']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary"
                                                            style="min-width: 45px;">{{ number_format($data['4-6 thn']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning text-dark"
                                                            style="min-width: 45px;">{{ number_format($data['7-10 thn']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-danger"
                                                            style="min-width: 45px;">{{ number_format($data['>10 thn']) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ number_format($totalLokasi) }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-secondary fw-bold">
                                                <td>TOTAL</td>
                                                <td class="text-center">{{ number_format($total01) }}</td>
                                                <td class="text-center">{{ number_format($total23) }}</td>
                                                <td class="text-center">{{ number_format($total46) }}</td>
                                                <td class="text-center">{{ number_format($total710) }}</td>
                                                <td class="text-center">{{ number_format($totalAbove10) }}</td>
                                                <td class="text-center">
                                                    {{ number_format($total01 + $total23 + $total46 + $total710 + $totalAbove10) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            <div class="tab-pane fade" id="lanjutan" role="tabpanel">
                {{-- Konten Lanjutan tidak berubah --}}
                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Distribusi Pendidikan Regional 1 (Organik)</h5>
                                <div class="chart-container" style="height:350px;"><canvas id="educationChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">📍 Distribusi Pendidikan per Lokasi</h5>
                                <p class="text-muted small mb-3">
                                    <strong>Analisis sebaran tingkat pendidikan</strong> di setiap lokasi geografis.<br>
                                    <i class="fa fa-info-circle text-info"></i> <strong>Data:</strong> Karyawan organik •
                                    Semua unit kerja • Breakdown per tingkat pendidikan
                                </p>

                                {{-- Tabel Distribusi Pendidikan per Lokasi --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">
                                                    Lokasi</th>
                                                <th class="text-center" colspan="9">Tingkat Pendidikan</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">
                                                    Total</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">
                                                    Skor</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">
                                                    Pendidikan Terbanyak</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">S3</th>
                                                <th class="text-center">S2</th>
                                                <th class="text-center">S1</th>
                                                <th class="text-center">D4</th>
                                                <th class="text-center">D3</th>
                                                <th class="text-center">D2</th>
                                                <th class="text-center">D1</th>
                                                <th class="text-center">SMA/SMK</th>
                                                <th class="text-center">Lainnya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($locationMatrix) && !empty($locationMatrix))
                                                @php
                                                    // Urutkan berdasarkan skor pendidikan (tertinggi ke terendah)
                                                    $sortedLocations = collect($locationMatrix)->sortByDesc(function (
                                                        $data,
                                                    ) {
                                                        return $data['education_score'] ?? 0;
                                                    });
                                                @endphp
                                                @foreach ($sortedLocations as $location => $data)
                                                    <tr class="{{ $loop->iteration <= 3 ? 'table-warning' : '' }}">
                                                        <td class="fw-semibold">
                                                            @if ($loop->iteration <= 3)
                                                                <span
                                                                    class="badge bg-warning text-dark me-2">{{ $loop->iteration }}</span>
                                                            @endif
                                                            {{ $location }}
                                                        </td>
                                                        <td class="text-center">{{ $data['S3'] ?? 0 }}</td>
                                                        <td class="text-center">{{ $data['S2'] ?? 0 }}</td>
                                                        <td class="text-center">{{ $data['S1'] ?? 0 }}</td>
                                                        <td class="text-center">{{ $data['D4'] ?? 0 }}</td>
                                                        <td class="text-center">{{ $data['D3'] ?? 0 }}</td>
                                                        <td class="text-center">{{ $data['D2'] ?? 0 }}</td>
                                                        <td class="text-center">{{ $data['D1'] ?? 0 }}</td>
                                                        <td class="text-center">{{ $data['SMA/SMK'] ?? 0 }}</td>
                                                        <td class="text-center">{{ $data['Lainnya'] ?? 0 }}</td>
                                                        <td class="text-center fw-bold">{{ $data['total'] ?? 0 }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-primary">
                                                                {{ number_format($data['education_score'] ?? 0, 1) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-success">
                                                                {{ $data['highest_education'] ?? '-' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                {{-- Row Total/Summary --}}
                                                <tr class="table-secondary fw-bold">
                                                    <td>TOTAL REGIONAL 1</td>
                                                    @php
                                                        $totals = [
                                                            'S3' => 0,
                                                            'S2' => 0,
                                                            'S1' => 0,
                                                            'D4' => 0,
                                                            'D3' => 0,
                                                            'D2' => 0,
                                                            'D1' => 0,
                                                            'SMA/SMK' => 0,
                                                            'Lainnya' => 0,
                                                            'total' => 0,
                                                        ];
                                                        foreach ($locationMatrix as $data) {
                                                            foreach ($totals as $key => $value) {
                                                                $totals[$key] += $data[$key] ?? 0;
                                                            }
                                                        }
                                                    @endphp
                                                    <td class="text-center">{{ $totals['S3'] }}</td>
                                                    <td class="text-center">{{ $totals['S2'] }}</td>
                                                    <td class="text-center">{{ $totals['S1'] }}</td>
                                                    <td class="text-center">{{ $totals['D4'] }}</td>
                                                    <td class="text-center">{{ $totals['D3'] }}</td>
                                                    <td class="text-center">{{ $totals['D2'] }}</td>
                                                    <td class="text-center">{{ $totals['D1'] }}</td>
                                                    <td class="text-center">{{ $totals['SMA/SMK'] }}</td>
                                                    <td class="text-center">{{ $totals['Lainnya'] }}</td>
                                                    <td class="text-center">{{ $totals['total'] }}</td>
                                                    <td class="text-center" colspan="2">
                                                        <span class="badge bg-dark">
                                                            {{ $totals['total'] > 0 ? number_format((($totals['S1'] + $totals['S2'] + $totals['S3']) / $totals['total']) * 100, 1) : 0 }}%
                                                            Sarjana+
                                                        </span>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="13" class="text-center text-muted py-4">
                                                        Data distribusi pendidikan tidak tersedia
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3 p-3 bg-light rounded-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="small text-muted">
                                                <strong>📊 Metodologi Penilaian Skor:</strong><br>
                                                • S3/Doktor: 9 poin | S2/Master: 8 poin | S1/Sarjana: 7 poin<br>
                                                • D4: 6 poin | D3: 5 poin | D2: 4 poin | D1: 3 poin<br>
                                                • SMA/SMK: 2 poin | Lainnya: 1 poin<br>
                                                • <strong>Skor Lokasi</strong> = AVG(poin semua karyawan dalam lokasi)
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="small text-muted">
                                                <strong>🎯 Interpretasi Data:</strong><br>
                                                • <strong>Tinggi (7-9):</strong> Dominasi S1/S2/S3<br>
                                                • <strong>Sedang (4-6):</strong> Mix diploma & sarjana<br>
                                                • <strong>Rendah (1-3):</strong> Dominasi SMA/SMK<br>
                                                • <span class="badge bg-warning text-dark">Top 3</span> = Lokasi dengan
                                                skor tertinggi<br>
                                                • <strong>Pendidikan Terbanyak:</strong> Tingkat pendidikan dengan jumlah
                                                karyawan paling banyak di lokasi tersebut
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Rencana Pensiun per Tahun --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fa-solid fa-calendar-check me-2 text-danger"></i>
                                    Distribusi Rencana Pensiun per Tahun
                                </h5>
                                <p class="text-muted small mb-3">
                                    <strong>Proyeksi pensiun karyawan</strong> berdasarkan tahun rencana pensiun.<br>
                                    <i class="fa fa-info-circle text-info"></i> <strong>Data:</strong> Karyawan organik •
                                    Grouped by tahun pensiun
                                </p>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 5%;">#</th>
                                                <th class="text-center" style="width: 20%;">Tahun Pensiun</th>
                                                <th class="text-center" style="width: 20%;">Jumlah Karyawan</th>
                                                <th style="width: 55%;">Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($retirementByYear) && $retirementByYear->count() > 0)
                                                @php
                                                    $totalRetirement = $retirementByYear->sum('total');
                                                @endphp
                                                @foreach ($retirementByYear as $retirement)
                                                    @php
                                                        $pct =
                                                            $totalRetirement > 0
                                                                ? ($retirement->total / $totalRetirement) * 100
                                                                : 0;
                                                        $currentYear = date('Y');
                                                        $yearsUntil = $retirement->tahun_pensiun - $currentYear;
                                                    @endphp
                                                    <tr
                                                        class="{{ $yearsUntil <= 5 && $yearsUntil >= 0 ? 'table-warning' : '' }}">
                                                        <td class="text-center text-muted fw-semibold">
                                                            {{ $loop->iteration }}</td>
                                                        <td class="text-center fw-bold">
                                                            {{ $retirement->tahun_pensiun }}
                                                            @if ($yearsUntil <= 5 && $yearsUntil >= 0)
                                                                <span class="badge bg-warning text-dark ms-2">
                                                                    <i class="fa-solid fa-exclamation-triangle"></i>
                                                                    {{ $yearsUntil }} tahun lagi
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-primary"
                                                                style="font-size: 1rem; padding: 0.5rem 1rem;">
                                                                {{ number_format($retirement->total) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="progress" style="height: 25px;">
                                                                <div class="progress-bar bg-gradient {{ $yearsUntil <= 5 && $yearsUntil >= 0 ? 'bg-warning' : 'bg-info' }}"
                                                                    style="width: {{ $pct }}%;">
                                                                    <small
                                                                        class="fw-semibold">{{ number_format($pct, 1) }}%</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr class="table-secondary fw-bold">
                                                    <td colspan="2" class="text-end">TOTAL KARYAWAN</td>
                                                    <td class="text-center">{{ number_format($totalRetirement) }}</td>
                                                    <td>
                                                        <span class="badge bg-dark"
                                                            style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                            100%
                                                        </span>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">
                                                        Data rencana pensiun tidak tersedia
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3 p-3 bg-light rounded-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="small text-muted">
                                                <strong>📌 Keterangan:</strong><br>
                                                • <span class="badge bg-warning text-dark">⚠️ 5 tahun lagi</span> =
                                                Karyawan yang akan pensiun dalam 5 tahun ke depan<br>
                                                • Data diambil dari kolom <code>rencana_pensiun</code><br>
                                                • Tahun pensiun diekstrak dari tanggal lengkap
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="small text-muted">
                                                <strong>🎯 Manfaat Data:</strong><br>
                                                • Perencanaan suksesi jabatan<br>
                                                • Proyeksi kebutuhan rekrutmen<br>
                                                • Manajemen talent pipeline<br>
                                                • Strategi knowledge transfer
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="unit" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tabel Komparasi per Unit (Pivot Unit x Lokasi)</h5>
                        <p class="text-muted small mb-3">
                            <i class="fa fa-search text-info"></i> Gunakan kolom pencarian untuk mencari unit tertentu
                        </p>
                        <div class="table-responsive">
                            <table id="unitLocationTable" class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Unit</th>
                                        @foreach ($allLocations as $lokasi)
                                            <th>{{ $lokasi }}</th>
                                        @endforeach
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sortedUnits = collect($allUnits)
                                            ->sortByDesc(function ($unit) use ($unitLocationPivot) {
                                                return $unitLocationPivot[$unit]['total'] ?? 0;
                                            })
                                            ->toArray();
                                    @endphp
                                    @foreach ($sortedUnits as $unit)
                                        <tr>
                                            <td><strong>{{ $unit }}</strong></td>
                                            @foreach ($allLocations as $lokasi)
                                                <td class="text-end">
                                                    {{ $unitLocationPivot[$unit][$lokasi] ?? 0 }}
                                                </td>
                                            @endforeach
                                            <td class="text-end fw-bold">{{ $unitLocationPivot[$unit]['total'] ?? 0 }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="small text-muted mt-2">* Setiap sel = jumlah karyawan pada unit & lokasi tsb</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('body-scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('bodNamesSection');
            if (!container) {
                return;
            }

            const input = container.querySelector('#bodSearchInput');
            const rows = Array.from(container.querySelectorAll('[data-bod-row]'));
            const emptyRow = container.querySelector('#bodSearchEmptyRow');
            const nameCells = Array.from(container.querySelectorAll('.cell-name'));

            if (!input || rows.length === 0) {
                return;
            }

            const escapeHtml = (value) =>
                value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');

            const highlightText = (text, keyword) => {
                if (!keyword) {
                    return escapeHtml(text);
                }

                const lowerText = text.toLowerCase();
                const lowerKeyword = keyword.toLowerCase();
                let searchIndex = lowerText.indexOf(lowerKeyword);

                if (searchIndex === -1) {
                    return escapeHtml(text);
                }

                let result = '';
                let lastIndex = 0;

                while (searchIndex !== -1) {
                    result += escapeHtml(text.slice(lastIndex, searchIndex));
                    result += '<mark class="bg-warning text-dark px-1 rounded-1">' +
                        escapeHtml(text.slice(searchIndex, searchIndex + keyword.length)) +
                        '</mark>';
                    lastIndex = searchIndex + keyword.length;
                    searchIndex = lowerText.indexOf(lowerKeyword, lastIndex);
                }

                result += escapeHtml(text.slice(lastIndex));
                return result;
            };

            nameCells.forEach((cell) => {
                if (!cell.dataset.originalText) {
                    cell.dataset.originalText = cell.textContent || '';
                }
            });

            const runFilter = () => {
                const rawQuery = input.value.trim();
                const query = rawQuery.toLowerCase();
                let visibleCount = 0;

                nameCells.forEach((cell) => {
                    const original = cell.dataset.originalText || '';
                    cell.innerHTML = highlightText(original, rawQuery);
                });

                rows.forEach((row) => {
                    if (!query) {
                        row.classList.remove('d-none');
                        visibleCount += 1;
                        return;
                    }

                    const text = row.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.classList.remove('d-none');
                        visibleCount += 1;
                    } else {
                        row.classList.add('d-none');
                    }
                });

                if (emptyRow) {
                    emptyRow.classList.toggle('d-none', visibleCount !== 0);
                }
            };

            input.addEventListener('input', runFilter);
            runFilter();
        });
    </script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables untuk tabel employees (jika ada)
            if ($('#employeesTable').length) {
                $('#employeesTable').DataTable({
                    "language": {
                        "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                    }
                });
            }

            // Inisialisasi DataTables untuk tabel Unit x Lokasi
            let unitTable = null;

            // Function untuk inisialisasi tabel unit
            function initUnitTable() {
                if (unitTable) {
                    unitTable.destroy();
                }
                unitTable = $('#unitLocationTable').DataTable({
                    "language": {
                        "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                    },
                    "pageLength": 25,
                    "order": [
                        [{{ count($allLocations ?? []) + 1 }}, 'desc']
                    ], // Sort by Total column
                    "columnDefs": [{
                            "orderable": false,
                            "targets": 0
                        } // Kolom Unit tetap bisa di-sort
                    ]
                });
            }

            if (window.ChartDataLabels) Chart.register(ChartDataLabels);
            if (window.ChartAnnotation) Chart.register(ChartAnnotation);

            const palette = {
                primary: '#4f46e5',
                secondary: '#db2777',
                amber: '#f97316',
                green: '#16a34a',
                violet: '#8b5cf6'
            };
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.plugins.legend.position = 'bottom';
            Chart.defaults.plugins.datalabels = {
                color: '#FFFFFF',
                font: {
                    weight: 'bold'
                },
                formatter: (v) => (v > 0 ? (v % 1 !== 0 ? v.toFixed(1) : v) : '')
            };

            let chartInstances = {};
            const createChart = (ctxId, type, data, options = {}) => {
                if (chartInstances[ctxId]) chartInstances[ctxId].destroy();
                const ctx = document.getElementById(ctxId);
                if (ctx) chartInstances[ctxId] = new Chart(ctx.getContext('2d'), {
                    type,
                    data,
                    options
                });
            };

            const chartInitializers = {
                '#dashboard': () => {
                    // Piramida Jabatan per Lokasi (horizontal grouped bar)
                    @if (isset($kkjLocationLabels) && isset($kkjLocationDatasets))
                        createChart('kkjLocationChart', 'bar', {
                            labels: @json($kkjLocationLabels),
                            datasets: @json($kkjLocationDatasets)
                        }, {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        weight: 'bold'
                                    },
                                    formatter: (value) => (value > 0 ? value : '')
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    beginAtZero: true
                                },
                                y: {
                                    stacked: true
                                }
                            }
                        });
                    @endif

                    // Gender per lokasi (horizontal grouped/stacked bar)
                    createChart('genderLocationChart', 'bar', {
                        labels: @json($genderLocationLabels),
                        datasets: @json($genderLocationDatasets)
                    }, {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: (value) => (value > 0 ? value : '')
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                beginAtZero: true
                            },
                            y: {
                                stacked: true
                            }
                        }
                    });

                    // Generasi per lokasi (horizontal grouped/stacked bar)
                    createChart('ageChart', 'bar', {
                        labels: @json($ageLocationLabels),
                        datasets: @json($ageLocationDatasets)
                    }, {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: (value) => (value > 0 ? value : '')
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                beginAtZero: true
                            },
                            y: {
                                stacked: true
                            }
                        }
                    });

                    // Masa kerja per lokasi (horizontal grouped/stacked bar)
                    createChart('tenureLocationChart', 'bar', {
                        labels: @json($tenureLocationLabels),
                        datasets: @json($tenureLocationDatasets)
                    }, {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: (value) => (value > 0 ? value : '')
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                beginAtZero: true
                            },
                            y: {
                                stacked: true
                            }
                        }
                    });
                },


                '#lanjutan': () => {
                    createChart('educationChart', 'bar', {
                        labels: @json($educationCounts->pluck('pendidikan_diakui')),
                        datasets: [{
                            data: @json($educationCounts->pluck('total')),
                            backgroundColor: palette.violet
                        }]
                    }, {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    });

                    // Chart distribusi pendidikan per lokasi
                    @if (isset($locationMatrix) && !empty($locationMatrix))
                        const locationData = @json($locationMatrix);
                        const educationLevels = @json($educationLevels) || ['SMP', 'SMA/SMK', 'D1',
                            'D2', 'D3', 'D4', 'S1', 'S2', 'S3'
                        ];
                        const locations = Object.keys(locationData);

                        const educationColors = {
                            'S3': '#ef9400',
                            'S2': '#ef00e5',
                            'S1': '#6900ef',
                            'D4': '#0018ef',
                            'D3': '#00efea',
                            'D2': '#00ef0a',
                            'D1': '#efef00',
                            'SMA/SMK': '#ef0000',
                            'SMP': '#0ABAB5'
                        };

                        const datasets = educationLevels.map(level => ({
                            label: level,
                            data: locations.map(location => locationData[location][level] || 0),
                            backgroundColor: educationColors[level] || palette.primary,
                            borderRadius: 2
                        }));

                        createChart('locationEducationChart', 'line', {
                            labels: locations,
                            datasets: datasets
                        }, {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                datalabels: {
                                    display: false // Nonaktifkan label untuk chart yang kompleks
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah Karyawan'
                                    }
                                }
                            }
                        });
                    @endif
                }
            };

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', (e) => {
                const target = e.target.getAttribute('data-bs-target');

                // Inisialisasi chart jika ada
                const initializer = chartInitializers[target];
                if (initializer) initializer();

                // Inisialisasi DataTable untuk tab unit
                if (target === '#unit') {
                    setTimeout(function() {
                        initUnitTable();
                    }, 100);
                }
            });

            // Inisialisasi tab pertama
            chartInitializers['#dashboard']();
        });
    </script>
@endpush
