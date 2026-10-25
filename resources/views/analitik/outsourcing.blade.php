@extends('layouts.app')

@section('title', 'Analitik Karyawan Outsourcing')
@section('header-title', 'Analitik Karyawan Outsourcing')

@push('head-scripts')
    {{-- Dependensi & CSS --}}
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

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
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- Total Karyawan --}}
        <div class="card mb-4">
            <div class="d-flex flex-column align-items-center justify-content-center p-3">
                <div style="background-color: #e0e7ff; color: #4338ca; width: 64px; height: 64px; border-radius: 50%; display: grid; place-items: center; font-size: 2rem; flex-shrink: 0;"
                    class="mb-2"><i class="fa-solid fa-users"></i></div>
                <div class="text-center">
                    <div style="font-size: 2.5rem; font-weight: 700;">{{ number_format($totalOutsourcing ?? 0) }}</div>
                    <div style="font-size: 1rem; color: var(--text-color-light);">Total Karyawan Outsourcing</div>
                </div>
            </div>
        </div>

        {{-- Tables --}}
        <div class="row g-4 mb-4">
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
                                        $genderData = [];
                                        foreach ($employees as $emp) {
                                            $lokasi = $emp->lokasi_kerja ?? '(Kosong)';
                                            $gender = $emp->jenis_kelamin ?? '(Kosong)';
                                            if (!isset($genderData[$lokasi])) {
                                                $genderData[$lokasi] = ['Laki-laki' => 0, 'Perempuan' => 0];
                                            }
                                            // Normalize gender untuk matching
                                            $normalizedGender =
                                                strtoupper($gender) === 'LAKI-LAKI'
                                                    ? 'Laki-laki'
                                                    : (strtoupper($gender) === 'PEREMPUAN'
                                                        ? 'Perempuan'
                                                        : $gender);
                                            if (isset($genderData[$lokasi][$normalizedGender])) {
                                                $genderData[$lokasi][$normalizedGender]++;
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
                                            <td class="text-center"><strong>{{ number_format($totalLokasi) }}</strong></td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-secondary fw-bold">
                                        <td>TOTAL</td>
                                        <td class="text-center">{{ number_format($totalLaki) }}</td>
                                        <td class="text-center">{{ number_format($totalPerempuan) }}</td>
                                        <td class="text-center">{{ number_format($totalLaki + $totalPerempuan) }}</td>
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
                                        $generasiData = [];
                                        foreach ($employees as $emp) {
                                            $lokasi = $emp->lokasi_kerja ?? '(Kosong)';
                                            // Hitung usia dari tanggal_lahir
                                            $usia = 0;
                                            if ($emp->tanggal_lahir) {
                                                try {
                                                    $parts = explode('/', $emp->tanggal_lahir);
                                                    if (count($parts) === 3) {
                                                        $birth = \Carbon\Carbon::createFromFormat(
                                                            'd/m/Y',
                                                            $emp->tanggal_lahir,
                                                        );
                                                        $usia = $birth->age;
                                                    }
                                                } catch (\Exception $e) {
                                                }
                                            }

                                            $generasi = 'Lainnya';
                                            if ($usia >= 18 && $usia <= 27) {
                                                $generasi = 'Gen Z';
                                            } elseif ($usia >= 28 && $usia <= 43) {
                                                $generasi = 'Milenial';
                                            } elseif ($usia >= 44 && $usia <= 59) {
                                                $generasi = 'Gen X';
                                            }

                                            if (!isset($generasiData[$lokasi])) {
                                                $generasiData[$lokasi] = ['Gen Z' => 0, 'Milenial' => 0, 'Gen X' => 0];
                                            }
                                            if (isset($generasiData[$lokasi][$generasi])) {
                                                $generasiData[$lokasi][$generasi]++;
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
                                            <td class="text-center"><strong>{{ number_format($totalLokasi) }}</strong></td>
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

            {{-- Tabel Instansi per Lokasi --}}
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-building me-2 text-info"></i>
                            Distribusi Instansi per Lokasi
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 30%;">Lokasi</th>
                                        <th style="width: 50%;">Instansi</th>
                                        <th class="text-center" style="width: 10%;">Jumlah</th>
                                        <th style="width: 10%;">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $instansiData = [];
                                        $lokasiTotal = [];
                                        foreach ($employees as $emp) {
                                            $lokasi = $emp->lokasi_kerja ?? '(Kosong)';
                                            $instansi = $emp->instansi ?? '(Kosong)';

                                            if (!isset($instansiData[$lokasi])) {
                                                $instansiData[$lokasi] = [];
                                            }
                                            if (!isset($instansiData[$lokasi][$instansi])) {
                                                $instansiData[$lokasi][$instansi] = 0;
                                            }
                                            $instansiData[$lokasi][$instansi]++;
                                            $lokasiTotal[$lokasi] = ($lokasiTotal[$lokasi] ?? 0) + 1;
                                        }
                                    @endphp
                                    @foreach ($instansiData as $lokasi => $instansis)
                                        @php
                                            $firstRow = true;
                                            $rowspan = count($instansis);
                                        @endphp
                                        @foreach ($instansis as $instansi => $jumlah)
                                            <tr>
                                                @if ($firstRow)
                                                    <td rowspan="{{ $rowspan }}" class="align-middle">
                                                        <strong>{{ $lokasi }}</strong>
                                                    </td>
                                                    @php $firstRow = false; @endphp
                                                @endif
                                                <td>{{ $instansi }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ number_format($jumlah) }}</span>
                                                </td>
                                                <td>
                                                    @php $pct = $lokasiTotal[$lokasi] > 0 ? ($jumlah / $lokasiTotal[$lokasi] * 100) : 0; @endphp
                                                    <div class="progress" style="height: 18px;">
                                                        <div class="progress-bar bg-info"
                                                            style="width: {{ $pct }}%;">
                                                            <small>{{ number_format($pct, 1) }}%</small>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Unit --}}
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-sitemap me-2 text-warning"></i>
                            Distribusi per Unit
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;" class="text-center">#</th>
                                        <th style="width: 60%;">Unit Kerja</th>
                                        <th class="text-center" style="width: 15%;">Jumlah</th>
                                        <th style="width: 20%;">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $unitData = [];
                                        foreach ($employees as $emp) {
                                            $unit = $emp->unit_kerja ?? '(Kosong)';
                                            $unitData[$unit] = ($unitData[$unit] ?? 0) + 1;
                                        }
                                        arsort($unitData);
                                        $totalUnit = array_sum($unitData);
                                    @endphp
                                    @foreach ($unitData as $unit => $jumlah)
                                        @php $pct = $totalUnit > 0 ? ($jumlah / $totalUnit * 100) : 0; @endphp
                                        <tr>
                                            <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                                            <td><strong>{{ $unit }}</strong></td>
                                            <td class="text-center">
                                                <span class="badge bg-warning text-dark"
                                                    style="min-width: 60px;">{{ number_format($jumlah) }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-warning"
                                                        style="width: {{ $pct }}%;">
                                                        <small
                                                            class="fw-semibold text-dark">{{ number_format($pct, 1) }}%</small>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="2" class="text-end">TOTAL KESELURUHAN</td>
                                        <td class="text-center">{{ number_format($totalUnit) }}</td>
                                        <td>
                                            <span class="badge bg-dark"
                                                style="font-size: 0.9rem; padding: 0.5rem 1rem;">100%</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Detail --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Tabel Komparasi Outsourcing (Pivot Unit x Instansi x Lokasi)</h5>
                <div class="table-responsive" style="max-width:100vw; overflow-x:auto;">
                    @php
                        // Kumpulkan semua lokasi unik
                        $allLocations = collect($employees)->pluck('lokasi_kerja')->unique()->sort()->values();
                        // Kumpulkan semua kombinasi unit & instansi
                        $unitInstansi = collect($employees)
                            ->map(function ($e) {
                                return [$e->unit_kerja, $e->instansi];
                            })
                            ->unique()
                            ->sortBy(function ($arr) {
                                return $arr[0] . '|' . $arr[1];
                            })
                            ->values();
                        // Bangun pivot: [unit][instansi][lokasi] = count
                        $pivot = [];
                        foreach ($employees as $e) {
                            $pivot[$e->unit_kerja][$e->instansi][$e->lokasi_kerja] =
                                ($pivot[$e->unit_kerja][$e->instansi][$e->lokasi_kerja] ?? 0) + 1;
                            $pivot[$e->unit_kerja][$e->instansi]['total'] =
                                ($pivot[$e->unit_kerja][$e->instansi]['total'] ?? 0) + 1;
                        }
                    @endphp
                    <table class="table table-bordered table-hover align-middle" id="pivotOutsourcingTable">
                        <thead class="table-light">
                            <tr>
                                <th>Unit</th>
                                <th>Instansi</th>
                                @foreach ($allLocations as $lokasi)
                                    <th>{{ $lokasi }}</th>
                                @endforeach
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unitInstansi as $pair)
                                @php [$unit, $instansi] = $pair; @endphp
                                <tr>
                                    <td><strong>{{ $unit }}</strong></td>
                                    <td>{{ $instansi }}</td>
                                    @foreach ($allLocations as $lokasi)
                                        <td class="text-end">{{ $pivot[$unit][$instansi][$lokasi] ?? 0 }}</td>
                                    @endforeach
                                    <td class="text-end fw-bold">{{ $pivot[$unit][$instansi]['total'] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="small text-muted mt-2">* Setiap sel = jumlah karyawan outsourcing pada kombinasi unit,
                    instansi,
                    dan lokasi tsb</div>
            </div>
        </div>


    </div>
@endsection

@push('body-scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // DataTable untuk tabel pivot outsourcing
            $('#pivotOutsourcingTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                },
                "lengthMenu": [10, 100, 1000],
                "pageLength": 10,
                "scrollX": true,
                "ordering": true
            });

            if (window.ChartDataLabels) Chart.register(ChartDataLabels);

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
                formatter: (value) => (value > 0 ? value : '')
            };

            const createChart = (ctxId, type, data, options = {}) => {
                const ctx = document.getElementById(ctxId);
                if (ctx) new Chart(ctx.getContext('2d'), {
                    type,
                    data,
                    options
                });
            };

            // Gender per Lokasi (Grouped Bar)
            createChart('genderChart', 'bar', {
                labels: @json($genderLocationLabels),
                datasets: @json($genderLocationDatasets)
            }, {
                responsive: true,
                maintainAspectRatio: false,
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
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                }
            });

            // Generasi per Lokasi
            createChart('ageChart', 'bar', {
                labels: @json($ageLocationLabels),
                datasets: @json($ageLocationDatasets)
            }, {
                responsive: true,
                maintainAspectRatio: false,
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
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                }
            });

            // Instansi per Lokasi
            createChart('instansiChart', 'bar', {
                labels: @json($instansiLocationLabels),
                datasets: @json($instansiLocationDatasets)
            }, {
                responsive: true,
                maintainAspectRatio: false,
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
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                }
            });

            // Unit
            createChart('unitChart', 'bar', {
                labels: @json($unitCounts->pluck('unit')),
                datasets: [{
                    label: 'Jumlah Karyawan',
                    data: @json($unitCounts->pluck('total')),
                    backgroundColor: palette.violet
                }]
            }, {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            });
        });
    </script>
@endpush
