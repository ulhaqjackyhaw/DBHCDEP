@extends('layouts.app')

@section('title', 'Data PGS')
@section('header-title', 'Data Pejabat Pengganti Sementara (PGS)')

@push('head-styles')
    <style>
        /* Force responsive behavior for all screen sizes */
        * {
            box-sizing: border-box;
        }

        body,
        main,
        .container,
        .container-fluid {
            overflow-x: hidden !important;
        }

        .table tbody tr.cursor-pointer:hover {
            background-color: #f8fafc !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .modal-body .form-control-plaintext {
            font-weight: 500;
            color: #334155;
            margin-bottom: 0;
            min-height: 38px;
            display: flex;
            align-items: center;
        }

        /* Container tabel - pastikan bisa scroll */
        .bg-white.rounded-xl {
            overflow: visible !important;
        }

        .table-responsive {
            border-radius: 0.5rem;
            overflow-x: auto !important;
            overflow-y: visible !important;
            -webkit-overflow-scrolling: touch;
            background-color: #ffffff !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
        }

        .table-responsive .table {
            background-color: #ffffff !important;
            margin-bottom: 0 !important;
            min-width: 1400px !important;
            width: max-content !important;
            table-layout: auto !important;
        }

        /* Scrollbar styling */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Ukuran font tabel - UBAH ANGKA DI SINI */
        .table-responsive .table,
        .table-responsive .table * {
            font-size: 10px !important;
        }

        /* Header tabel */
        .table-responsive .table thead th,
        .table-responsive .table thead th * {
            font-size: 10px !important;
            padding: 0.75rem !important;
            white-space: nowrap !important;
        }

        /* Body tabel */
        .table-responsive .table tbody td,
        .table-responsive .table tbody td *,
        .table-responsive .table tbody span,
        .table-responsive .table tbody .badge {
            font-size: 10px !important;
            padding: 0.75rem !important;
        }

        /* Tombol dalam tabel */
        .table-responsive .table .btn-sm,
        .table-responsive .table .btn-sm i {
            font-size: 9px !important;
            padding: 0.25rem 0.5rem !important;
        }

        /* Icon sort */
        .table-responsive .table i {
            font-size: 10px !important;
        }

        /* Media queries untuk berbagai ukuran layar */
        @media (max-width: 1920px) {
            .table-responsive {
                overflow-x: auto !important;
            }
        }

        @media (max-width: 1366px) {
            .table-responsive .table {
                min-width: 1200px !important;
            }
        }

        @media (max-width: 768px) {
            .table-responsive .table {
                min-width: 1000px !important;
            }

            .table-responsive .table thead th,
            .table-responsive .table tbody td {
                padding: 0.5rem !important;
            }
        }

        .btn-outline-info:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        .durasi-overdue {
            color: #dc2626 !important;
            font-weight: 700;
            animation: pulse-red 2s ease-in-out infinite;
        }

        .durasi-warning {
            color: #f59e0b !important;
            font-weight: 700;
            animation: pulse-yellow 2s ease-in-out infinite;
        }

        @keyframes pulse-red {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        @keyframes pulse-yellow {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .badge-overdue {
            animation: shake 0.5s ease-in-out;
        }

        .badge-warning-pgs {
            animation: pulse 1s ease-in-out infinite;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Clickable alert styling */
        .alert.cursor-pointer:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .alert.cursor-pointer {
            transition: all 0.3s ease;
        }
    </style>
@endpush

@section('content')
    {{-- Notifikasi Sukses dan Error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Notifikasi Peringatan PGS Melebihi Batas --}}
    @php
        $overdueList = $dataPgs->filter(fn($pgs) => $pgs->is_overdue);
        $warningList = $dataPgs->filter(fn($pgs) => $pgs->is_warning);
        $overdueCount = $overdueList->count();
        $warningCount = $warningList->count();
    @endphp
    @if ($overdueCount > 0)
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-start cursor-pointer"
            role="alert" data-bs-toggle="modal" data-bs-target="#overdueModal" style="cursor: pointer;">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div class="flex-grow-1">
                <strong>🚨 Peringatan Merah!</strong> Terdapat <strong>{{ $overdueCount }}</strong> karyawan PGS yang sudah
                melewati tanggal selesai PGS.
                <br>
                <small class="text-muted">Klik untuk melihat daftar karyawan yang sudah melewati batas waktu.</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                onclick="event.stopPropagation();"></button>
        </div>
    @endif
    @if ($warningCount > 0)
        <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 d-flex align-items-start cursor-pointer"
            role="alert" data-bs-toggle="modal" data-bs-target="#warningModal" style="cursor: pointer;">
            <i class="bi bi-exclamation-circle-fill me-3 fs-4"></i>
            <div class="flex-grow-1">
                <strong>⚠️ Peringatan Kuning!</strong> Terdapat <strong>{{ $warningCount }}</strong> karyawan PGS yang akan
                selesai dalam 15 hari ke depan.
                <br>
                <small class="text-muted">Klik untuk melihat daftar karyawan yang akan segera selesai masa PGS.</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                onclick="event.stopPropagation();"></button>
        </div>
    @endif

    @can('admin')
        {{-- Bagian Upload Massal --}}
        <div class="bg-white p-4 sm:p-5 rounded-xl shadow-sm mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h2 class="text-lg font-semibold text-slate-800 mb-0">Upload Data Massal</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('data-pgs.template.download') }}"
                        class="btn btn-outline-success d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                        <span>Download Template</span>
                    </a>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <form action="{{ route('data-pgs.import.add') }}" method="POST" enctype="multipart/form-data"
                        class="d-flex gap-3">
                        @csrf
                        <input type="file" name="file" class="form-control" required accept=".xlsx,.xls,.csv">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 text-nowrap">
                            <i class="bi bi-cloud-arrow-up-fill"></i> Tambah
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('data-pgs.import.replace') }}" method="POST" enctype="multipart/form-data"
                        class="d-flex gap-3">
                        @csrf
                        <input type="file" name="file" class="form-control" required accept=".xlsx,.xls,.csv">
                        <button type="submit" class="btn btn-danger d-flex align-items-center gap-2 text-nowrap">
                            <i class="bi bi-arrow-repeat"></i> Ganti Semua
                        </button>
                    </form>
                </div>
            </div>

            {{-- Penjelasan Mode Upload --}}
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <div class="alert alert-info mb-0 py-2">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill me-2 mt-1 text-info"></i>
                            <div>
                                <strong>Mode Tambah:</strong><br>
                                <small>Data baru akan ditambahkan ke database. Data lama tetap ada dan tidak akan
                                    terhapus.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-warning mb-0 py-2">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill me-2 mt-1 text-warning"></i>
                            <div>
                                <strong>Mode Ganti Semua:</strong><br>
                                <small>SEMUA data lama akan dihapus dan diganti dengan data dari file yang
                                    diupload.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- Tabel Data PGS --}}
    <div class="bg-white rounded-xl shadow-sm" x-data="pgsTable({{ $dataPgs->toJson() ?? '[]' }})">
        <div class="p-4 sm:p-5">
            {{-- Header Tabel --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @can('admin')
                        <a href="{{ route('data-pgs.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span class="text-nowrap">Tambah Data PGS</span>
                        </a>
                        <a href="{{ route('data-pgs.export') }}" class="btn btn-outline-info d-flex align-items-center gap-2">
                            <i class="bi bi-download"></i>
                            <span>Download Data PGS</span>
                        </a>
                    @endcan
                </div>

                {{-- Sisi Kanan: Filter dan Search --}}
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <label for="itemsPerPage" class="form-label text-nowrap mb-0 text-slate-600">Tampilkan</label>
                        <select id="itemsPerPage" class="form-select form-select-sm" style="width: auto;"
                            x-model.number="itemsPerPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-slate-600 text-nowrap">data</span>
                    </div>
                    <div style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Cari data PGS..."
                            x-model.debounce.300ms="searchTerm">
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-slate-300 font-semibold text-nowrap">No</th>
                            <th @click="sortBy('nik')"
                                class="text-slate-300 font-semibold cursor-pointer user-select-none text-nowrap">
                                NIK <i :class="sortIcon('nik')"></i>
                            </th>
                            <th @click="sortBy('nama')"
                                class="text-slate-300 font-semibold cursor-pointer user-select-none text-nowrap">
                                Nama <i :class="sortIcon('nama')"></i>
                            </th>
                            <th @click="sortBy('jabatan_definitif')"
                                class="text-slate-300 font-semibold cursor-pointer user-select-none text-nowrap">
                                Jabatan Definitif <i :class="sortIcon('jabatan_definitif')"></i>
                            </th>
                            <th @click="sortBy('jabatan_pgs')"
                                class="text-slate-300 font-semibold cursor-pointer user-select-none text-nowrap">
                                Jabatan PGS <i :class="sortIcon('jabatan_pgs')"></i>
                            </th>
                            <th @click="sortBy('lokasi_unit_kerja')"
                                class="text-slate-300 font-semibold cursor-pointer user-select-none text-nowrap">
                                Lokasi/Unit Kerja <i :class="sortIcon('lokasi_unit_kerja')"></i>
                            </th>
                            <th @click="sortBy('tanggal_pgs_formatted')"
                                class="text-slate-300 font-semibold cursor-pointer user-select-none text-nowrap">
                                Tanggal Mulai PGS <i :class="sortIcon('tanggal_pgs_formatted')"></i>
                            </th>
                            <th @click="sortBy('tanggal_selesai_pgs_formatted')"
                                class="text-slate-300 font-semibold cursor-pointer user-select-none text-nowrap">
                                Tanggal Selesai PGS <i :class="sortIcon('tanggal_selesai_pgs_formatted')"></i>
                            </th>
                            <th @click="sortBy('durasi_pgs')"
                                class="text-slate-300 font-semibold cursor-pointer user-select-none text-nowrap">
                                Durasi <i :class="sortIcon('durasi_pgs')"></i>
                            </th>
                            <th class="text-slate-300 font-semibold text-nowrap">Status</th>
                            <th class="text-slate-300 font-semibold text-nowrap">Detail</th>
                            @can('admin')
                                <th class="text-slate-300 font-semibold text-nowrap">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(pgs, index) in paginatedData" :key="pgs.id">
                            <tr class="text-slate-700 cursor-pointer" @click="showPgsDetail(pgs)"
                                title="Klik untuk melihat detail">
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td><span class="badge bg-light text-dark border" x-text="pgs.nik"></span></td>
                                <td x-text="pgs.nama"></td>
                                <td x-text="pgs.jabatan_definitif"></td>
                                <td x-text="pgs.jabatan_pgs"></td>
                                <td x-text="pgs.lokasi_unit_kerja"></td>
                                <td x-text="pgs.tanggal_pgs_formatted"></td>
                                <td>
                                    <span x-text="pgs.tanggal_selesai_pgs_formatted || '-'"></span>
                                </td>
                                <td x-text="pgs.durasi_pgs"></td>
                                <td>
                                    <template x-if="pgs.is_overdue">
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 badge-overdue">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            <span x-text="pgs.sisa_hari"></span>
                                        </span>
                                    </template>
                                    <template x-if="!pgs.is_overdue && pgs.is_warning">
                                        <span
                                            class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 badge-warning-pgs">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                                            <span x-text="pgs.sisa_hari"></span>
                                        </span>
                                    </template>
                                    <template x-if="!pgs.is_overdue && !pgs.is_warning && pgs.tanggal_selesai_pgs">
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            <span x-text="pgs.sisa_hari"></span>
                                        </span>
                                    </template>
                                    <template x-if="!pgs.tanggal_selesai_pgs">
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                            Belum diatur
                                        </span>
                                    </template>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info" title="Lihat Detail"
                                        @click.stop="showPgsDetail(pgs)">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </td>
                                @can('admin')
                                    <td>
                                        <div class="d-flex gap-2" @click.stop>
                                            <a :href="`/data-pgs/${pgs.id}/edit`" class="btn btn-sm btn-outline-warning"
                                                title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                                @click="deleteUrl = `/data-pgs/${pgs.id}`">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        </template>
                        <tr x-show="!paginatedData.length">
                            <td :colspan="@can('admin') 11 @else 10 @endcan" class="text-center text-muted py-5">
                                <span x-show="dataPgs.length > 0">Data tidak ditemukan.</span>
                                <span x-show="dataPgs.length === 0">Belum ada data PGS.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                <div class="text-slate-600">
                    Menampilkan <span x-text="Math.min((currentPage - 1) * itemsPerPage + 1, sortedData.length)"></span>
                    sampai <span x-text="Math.min(currentPage * itemsPerPage, sortedData.length)"></span>
                    dari <span x-text="sortedData.length"></span> data
                </div>
                <nav x-show="totalPages > 1">
                    <ul class="pagination mb-0">
                        <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                            <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">Previous</a>
                        </li>
                        <template x-for="page in pages">
                            <li class="page-item"
                                :class="{ 'active': page === currentPage, 'disabled': page === '...' }">
                                <a class="page-link" href="#" @click.prevent="if (page !== '...') changePage(page)"
                                    x-text="page"></a>
                            </li>
                        </template>
                        <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                            <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        {{-- Modal Detail Data PGS --}}
        <div class="modal fade" id="pgsDetailModal" tabindex="-1" aria-labelledby="pgsDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h1 class="modal-title fs-5" id="pgsDetailModalLabel">
                            <i class="bi bi-person-badge me-2"></i>Detail Data PGS
                        </h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" x-show="selectedPgs">
                        <div class="row g-3" x-show="selectedPgs">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">NIK</label>
                                <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                    x-text="selectedPgs?.nik || '-'"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Nama</label>
                                <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                    x-text="selectedPgs?.nama || '-'"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Jabatan Definitif</label>
                                <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                    x-text="selectedPgs?.jabatan_definitif || '-'"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Jabatan PGS</label>
                                <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                    x-text="selectedPgs?.jabatan_pgs || '-'">
                                </p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-muted small">Lokasi/Unit Kerja</label>
                                <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                    x-text="selectedPgs?.lokasi_unit_kerja || '-'"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Tanggal Mulai PGS</label>
                                <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                    <i class="bi bi-calendar3 text-muted me-1"></i>
                                    <span x-text="selectedPgs?.tanggal_pgs_formatted || '-'"></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Tanggal Selesai PGS</label>
                                <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                    <i class="bi bi-calendar-check text-muted me-1"></i>
                                    <span x-text="selectedPgs?.tanggal_selesai_pgs_formatted || '-'"></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Durasi PGS</label>
                                <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                    <i class="bi bi-hourglass-split text-muted me-1"></i>
                                    <span x-text="selectedPgs?.durasi_pgs || '-'"></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Status</label>
                                <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                    <template x-if="selectedPgs?.is_overdue">
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger badge-overdue">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            <span x-text="selectedPgs?.sisa_hari"></span>
                                        </span>
                                    </template>
                                    <template x-if="!selectedPgs?.is_overdue && selectedPgs?.is_warning">
                                        <span
                                            class="badge bg-warning bg-opacity-10 text-warning border border-warning badge-warning-pgs">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                                            <span x-text="selectedPgs?.sisa_hari"></span>
                                        </span>
                                    </template>
                                    <template
                                        x-if="!selectedPgs?.is_overdue && !selectedPgs?.is_warning && selectedPgs?.tanggal_selesai_pgs">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            <span x-text="selectedPgs?.sisa_hari"></span>
                                        </span>
                                    </template>
                                    <template x-if="!selectedPgs?.tanggal_selesai_pgs">
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                            Belum diatur
                                        </span>
                                    </template>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Tutup
                        </button>
                        @can('admin')
                            <a x-show="selectedPgs" :href="selectedPgs ? `/data-pgs/${selectedPgs.id}/edit` : '#'"
                                class="btn btn-warning">
                                <i class="bi bi-pencil-fill me-1"></i>Edit Data
                            </a>
                            <button type="button" x-show="selectedPgs" class="btn btn-danger"
                                @click="confirmDelete(selectedPgs.id)">
                                <i class="bi bi-trash-fill me-1"></i>Hapus Data
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Delete Confirmation --}}
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteModalLabel">
                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Konfirmasi Hapus Data
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda benar-benar yakin ingin menghapus data ini? Proses ini tidak dapat diurungkan.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form id="deleteForm" :action="deleteUrl" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Daftar Overdue --}}
        <div class="modal fade" id="overdueModal" tabindex="-1" aria-labelledby="overdueModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h1 class="modal-title fs-5" id="overdueModalLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Daftar Karyawan PGS Overdue
                        </h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger mb-3">
                            <strong>Perhatian!</strong> {{ $overdueCount }} karyawan berikut sudah melewati tanggal selesai
                            PGS dan memerlukan tindakan segera.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-danger">
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Jabatan PGS</th>
                                        <th>Lokasi/Unit Kerja</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($overdueList as $index => $pgs)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><span class="badge bg-dark">{{ $pgs->nik }}</span></td>
                                            <td><strong>{{ $pgs->nama }}</strong></td>
                                            <td>{{ $pgs->jabatan_pgs }}</td>
                                            <td>{{ $pgs->lokasi_unit_kerja }}</td>
                                            <td>{{ $pgs->tanggal_selesai_pgs_formatted }}</td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                    {{ $pgs->sisa_hari }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        @click="switchToDetail({{ $pgs->id }})"
                                                        title="Lihat Detail">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                    @can('admin')
                                                        <a href="{{ route('data-pgs.edit', $pgs->id) }}"
                                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Tidak ada data overdue.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Daftar Warning --}}
        <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h1 class="modal-title fs-5" id="warningModalLabel">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>Daftar Karyawan PGS Mendekati Batas Waktu
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning mb-3">
                            <strong>Peringatan!</strong> {{ $warningCount }} karyawan berikut akan menyelesaikan masa PGS
                            dalam 15 hari ke depan.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-warning">
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Jabatan PGS</th>
                                        <th>Lokasi/Unit Kerja</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($warningList as $index => $pgs)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><span class="badge bg-dark">{{ $pgs->nik }}</span></td>
                                            <td><strong>{{ $pgs->nama }}</strong></td>
                                            <td>{{ $pgs->jabatan_pgs }}</td>
                                            <td>{{ $pgs->lokasi_unit_kerja }}</td>
                                            <td>{{ $pgs->tanggal_selesai_pgs_formatted }}</td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-exclamation-circle-fill me-1"></i>
                                                    {{ $pgs->sisa_hari }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        @click="switchToDetail({{ $pgs->id }})"
                                                        title="Lihat Detail">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                    @can('admin')
                                                        <a href="{{ route('data-pgs.edit', $pgs->id) }}"
                                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Tidak ada data dalam
                                                peringatan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('body-scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pgsTable', (initialData = []) => ({
                dataPgs: initialData,
                searchTerm: '',
                sortColumn: 'created_at',
                sortDirection: 'desc',
                itemsPerPage: 10,
                currentPage: 1,
                deleteUrl: '',
                selectedPgs: null,

                get filteredData() {
                    if (this.searchTerm === '') return this.dataPgs;
                    const term = this.searchTerm.toLowerCase();
                    return this.dataPgs.filter(pgs =>
                        Object.values(pgs).some(value =>
                            String(value).toLowerCase().includes(term)
                        )
                    );
                },

                get sortedData() {
                    return [...this.filteredData].sort((a, b) => {
                        let colA = a[this.sortColumn];
                        let colB = b[this.sortColumn];

                        // Handle null/undefined
                        if (colA == null && colB == null) return 0;
                        if (colA == null) return this.sortDirection === 'asc' ? -1 : 1;
                        if (colB == null) return this.sortDirection === 'asc' ? 1 : -1;

                        // Cek apakah keduanya angka
                        const numA = Number(colA);
                        const numB = Number(colB);

                        let comparison = 0;
                        if (!isNaN(numA) && !isNaN(numB)) {
                            // Bandingkan sebagai angka
                            comparison = numA - numB;
                        } else {
                            // Bandingkan sebagai string
                            comparison = String(colA).localeCompare(String(colB));
                        }

                        return this.sortDirection === 'asc' ? comparison : -comparison;
                    });
                },

                get paginatedData() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.sortedData.slice(start, end);
                },

                get totalPages() {
                    return Math.ceil(this.sortedData.length / this.itemsPerPage);
                },

                get pages() {
                    const maxPages = 7;
                    const total = this.totalPages;
                    const current = this.currentPage;
                    if (total <= maxPages) {
                        return Array.from({
                            length: total
                        }, (_, i) => i + 1);
                    }
                    const pagesArray = [1];
                    let start = Math.max(2, current - 2);
                    let end = Math.min(total - 1, current + 2);
                    if (current < 4) {
                        end = 5;
                    }
                    if (current > total - 3) {
                        start = total - 4;
                    }
                    if (start > 2) pagesArray.push('...');
                    for (let i = start; i <= end; i++) {
                        pagesArray.push(i);
                    }
                    if (end < total - 1) pagesArray.push('...');
                    pagesArray.push(total);
                    return pagesArray;
                },

                sortBy(column) {
                    if (this.sortColumn === column) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumn = column;
                        this.sortDirection = 'asc';
                    }
                },

                sortIcon(column) {
                    if (this.sortColumn !== column) return 'bi bi-arrow-down-up opacity-25';
                    return this.sortDirection === 'asc' ? 'bi bi-sort-up-alt' : 'bi bi-sort-down';
                },

                changePage(page) {
                    if (page < 1 || page > this.totalPages) return;
                    this.currentPage = page;
                    document.querySelector('main')?.scrollTo(0, 0);
                },

                showPgsDetail(pgs) {
                    this.selectedPgs = pgs;
                    const modal = new bootstrap.Modal(document.getElementById('pgsDetailModal'));
                    modal.show();
                },

                switchToDetail(pgsId) {
                    // Find the PGS data
                    const pgs = this.dataPgs.find(p => p.id === pgsId);
                    if (!pgs) return;

                    // Close all open modals first
                    const openModals = document.querySelectorAll('.modal.show');
                    openModals.forEach(modalEl => {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    });

                    // Wait for modal to fully close, then open detail modal
                    setTimeout(() => {
                        this.showPgsDetail(pgs);
                    }, 400);
                },

                confirmDelete(pgsId) {
                    // Set delete URL
                    this.deleteUrl = `/data-pgs/${pgsId}`;

                    // Close detail modal first
                    const detailModal = bootstrap.Modal.getInstance(document.getElementById(
                        'pgsDetailModal'));
                    if (detailModal) {
                        detailModal.hide();
                    }

                    // Wait for modal to close, then open delete confirmation
                    setTimeout(() => {
                        const deleteModal = new bootstrap.Modal(document.getElementById(
                            'deleteConfirmationModal'));
                        deleteModal.show();
                    }, 400);
                },

                init() {
                    this.$watch('searchTerm', () => this.currentPage = 1);
                    this.$watch('itemsPerPage', () => this.currentPage = 1);
                }
            }));
        });
    </script>
@endpush
