@extends('layouts.app')

@section('title', 'Data Penugasan')
@section('header-title', 'Data Penugasan Karyawan')

@push('head-styles')
    <style>
        /* Gaya dasar tabel Bootstrap */
        #penugasan-page .table {
            --bs-table-color: var(--bs-body-color);
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--bs-border-color);
            --bs-table-accent-bg: transparent;
            --bs-table-striped-color: var(--bs-body-color);
            --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
            --bs-table-active-color: var(--bs-body-color);
            --bs-table-active-bg: rgba(0, 0, 0, 0.1);
            --bs-table-hover-color: var(--bs-body-color);
            --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
            width: 100%;
            margin-bottom: 1rem;
            vertical-align: top;
            border-color: var(--bs-border-color);
            caption-side: bottom;
            border-collapse: collapse;
        }

        #penugasan-page .table> :not(caption)>*>* {
            padding: 0.5rem 0.6rem;
            background-color: var(--bs-table-bg);
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
            font-size: 0.875rem;
        }

        #penugasan-page .table>thead {
            vertical-align: bottom;
        }

        #penugasan-page .table thead th {
            font-size: 0.8rem;
            font-weight: 600;
        }

        #penugasan-page .table-hover>tbody>tr:hover>* {
            --bs-table-accent-bg: var(--bs-table-hover-bg);
        }

        #penugasan-page .table tbody tr.cursor-pointer:hover {
            background-color: #f8fafc !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        /* Tampilan kartu responsif untuk mobile */
        @media (max-width: 991.98px) {
            #penugasan-page .table thead {
                display: none;
            }

            #penugasan-page .table tr {
                display: block;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
                overflow: hidden;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            }

            #penugasan-page .table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem !important;
                text-align: right !important;
                border: none;
                border-bottom: 1px solid #f1f5f9;
                word-break: break-word;
            }

            #penugasan-page .table td:last-child {
                border-bottom: none;
            }

            #penugasan-page .table td:before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
                padding-right: 1rem;
                color: #334155;
                flex-shrink: 0;
            }
        }

        .modal-body .form-control-plaintext {
            font-weight: 500;
            color: #334155;
            margin-bottom: 0;
            min-height: 38px;
            display: flex;
            align-items: center;
        }

        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
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

        .badge-warning-penugasan {
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
    <div id="penugasan-page">
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
            $overdueList = $dataPenugasan->filter(fn($penugasan) => $penugasan->is_overdue);
            $warningList = $dataPenugasan->filter(fn($penugasan) => $penugasan->is_warning);
            $overdueCount = $overdueList->count();
            $warningCount = $warningList->count();
        @endphp
        @if ($overdueCount > 0)
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-start cursor-pointer"
                role="alert" data-bs-toggle="modal" data-bs-target="#overdueModal" style="cursor: pointer;">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                <div class="flex-grow-1">
                    <strong>🚨 Peringatan Merah!</strong> Terdapat <strong>{{ $overdueCount }}</strong> karyawan yang sudah
                    melewati tanggal selesai penugasan.
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
                    <strong>⚠️ Peringatan Kuning!</strong> Terdapat <strong>{{ $warningCount }}</strong> karyawan yang akan
                    menyelesaikan masa penugasan dalam 15 hari ke depan.
                    <br>
                    <small class="text-muted">Klik untuk melihat daftar karyawan yang mendekati batas waktu.</small>
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
                        <a href="{{ route('data-penugasan.template') }}"
                            class="btn btn-outline-success d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-arrow-down-fill"></i>
                            <span>Download Template</span>
                        </a>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <form action="{{ route('data-penugasan.import.add') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex gap-3">
                            @csrf
                            <input type="file" name="file" class="form-control" required accept=".xlsx,.xls,.csv">
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 text-nowrap">
                                <i class="bi bi-cloud-arrow-up-fill"></i> Tambah
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('data-penugasan.import.replace') }}" method="POST" enctype="multipart/form-data"
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

        {{-- Tabel Data Penugasan --}}
        <div class="bg-white rounded-xl shadow-sm" x-data="penugasanTable({{ $dataPenugasan->toJson() ?? '[]' }})">
            <div class="p-4 sm:p-5">
                {{-- Header Tabel --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @can('admin')
                            <a href="{{ route('data-penugasan.create') }}"
                                class="btn btn-primary d-flex align-items-center gap-2">
                                <i class="bi bi-plus-circle-fill"></i>
                                <span class="text-nowrap">Tambah Data Penugasan</span>
                            </a>
                            <a href="{{ route('data-penugasan.export') }}"
                                class="btn btn-outline-info d-flex align-items-center gap-2">
                                <i class="bi bi-download"></i>
                                <span>Download Data Penugasan</span>
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
                            <input type="text" class="form-control" placeholder="Cari data penugasan..."
                                x-model.debounce.300ms="searchTerm">
                        </div>
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-slate-500 font-semibold text-nowrap">No</th>
                                <th @click="sortBy('nik')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">
                                    NIK <i :class="sortIcon('nik')"></i>
                                </th>
                                <th @click="sortBy('nama')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">
                                    Nama <i :class="sortIcon('nama')"></i>
                                </th>
                                <th @click="sortBy('jabatan_definitif')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">
                                    Jabatan Definitif <i :class="sortIcon('jabatan_definitif')"></i>
                                </th>
                                <th @click="sortBy('unit_penugasan')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">
                                    Unit Penugasan <i :class="sortIcon('unit_penugasan')"></i>
                                </th>
                                <th @click="sortBy('lokasi_penugasan')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">
                                    Lokasi Penugasan <i :class="sortIcon('lokasi_penugasan')"></i>
                                </th>
                                <th @click="sortBy('tanggal_mulai_formatted')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">
                                    Tanggal Mulai <i :class="sortIcon('tanggal_mulai_formatted')"></i>
                                </th>
                                <th @click="sortBy('tanggal_selesai_formatted')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">
                                    Tanggal Selesai <i :class="sortIcon('tanggal_selesai_formatted')"></i>
                                </th>
                                <th @click="sortBy('durasi_penugasan')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">
                                    Durasi <i :class="sortIcon('durasi_penugasan')"></i>
                                </th>
                                <th class="text-slate-500 font-semibold text-nowrap">Status</th>
                                <th class="text-slate-500 font-semibold text-nowrap">Detail</th>
                                @can('admin')
                                    <th class="text-slate-500 font-semibold text-nowrap">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(penugasan, index) in paginatedData" :key="penugasan.id">
                                <tr class="text-slate-700 cursor-pointer" @click="showPenugasanDetail(penugasan)"
                                    title="Klik untuk melihat detail">
                                    <td data-label="No" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                    <td data-label="NIK"><span class="badge bg-light text-dark border"
                                            x-text="penugasan.nik"></span></td>
                                    <td data-label="Nama" x-text="penugasan.nama"></td>
                                    <td data-label="Jabatan Definitif" x-text="penugasan.jabatan_definitif"></td>
                                    <td data-label="Unit Penugasan" x-text="penugasan.unit_penugasan"></td>
                                    <td data-label="Lokasi Penugasan" x-text="penugasan.lokasi_penugasan"></td>
                                    <td data-label="Tanggal Mulai" x-text="penugasan.tanggal_mulai_formatted"></td>
                                    <td data-label="Tanggal Selesai">
                                        <span x-text="penugasan.tanggal_selesai_formatted || '-'"></span>
                                    </td>
                                    <td data-label="Durasi" x-text="penugasan.durasi_penugasan"></td>
                                    <td data-label="Status">
                                        <template x-if="penugasan.is_overdue">
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 badge-overdue">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                <span x-text="penugasan.sisa_hari"></span>
                                            </span>
                                        </template>
                                        <template x-if="!penugasan.is_overdue && penugasan.is_warning">
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 badge-warning-penugasan">
                                                <i class="bi bi-exclamation-circle-fill me-1"></i>
                                                <span x-text="penugasan.sisa_hari"></span>
                                            </span>
                                        </template>
                                        <template
                                            x-if="!penugasan.is_overdue && !penugasan.is_warning && penugasan.tanggal_selesai">
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                <span x-text="penugasan.sisa_hari"></span>
                                            </span>
                                        </template>
                                        <template x-if="!penugasan.tanggal_selesai">
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                                Belum diatur
                                            </span>
                                        </template>
                                    </td>
                                    <td data-label="Detail">
                                        <button type="button" class="btn btn-sm btn-outline-info" title="Lihat Detail"
                                            @click.stop="showPenugasanDetail(penugasan)">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                    </td>
                                    @can('admin')
                                        <td data-label="Aksi">
                                            <div class="d-flex gap-2" @click.stop>
                                                <a :href="`/data-penugasan/${penugasan.id}/edit`"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                    data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                                    @click="deleteUrl = `/data-penugasan/${penugasan.id}`">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            </template>
                            <tr x-show="!paginatedData.length">
                                <td :colspan="@can('admin') 12 @else 11 @endcan" class="text-center text-muted py-5">
                                    <span x-show="dataPenugasan.length > 0">Data tidak ditemukan.</span>
                                    <span x-show="dataPenugasan.length === 0">Belum ada data penugasan.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                    <div class="text-slate-600">
                        Menampilkan <span
                            x-text="Math.min((currentPage - 1) * itemsPerPage + 1, sortedData.length)"></span>
                        sampai <span x-text="Math.min(currentPage * itemsPerPage, sortedData.length)"></span>
                        dari <span x-text="sortedData.length"></span> data
                    </div>
                    <nav x-show="totalPages > 1">
                        <ul class="pagination mb-0">
                            <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(currentPage - 1)">Previous</a>
                            </li>
                            <template x-for="page in pages">
                                <li class="page-item"
                                    :class="{ 'active': page === currentPage, 'disabled': page === '...' }">
                                    <a class="page-link" href="#"
                                        @click.prevent="if (page !== '...') changePage(page)" x-text="page"></a>
                                </li>
                            </template>
                            <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                                <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            {{-- Modal Detail Data Penugasan --}}
            <div class="modal fade" id="penugasanDetailModal" tabindex="-1" aria-labelledby="penugasanDetailModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h1 class="modal-title fs-5" id="penugasanDetailModalLabel">
                                <i class="bi bi-person-badge me-2"></i>Detail Data Penugasan
                            </h1>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" x-show="selectedPenugasan">
                            <div class="row g-3" x-show="selectedPenugasan">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">NIK</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.nik || '-'"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Nama</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.nama || '-'"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Unit Definitif</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.unit_definitif || '-'"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Jabatan Definitif</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.jabatan_definitif || '-'"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Lokasi Definitif</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.lokasi_definitif || '-'"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">KJ (Kelompok Jabatan)</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.kj || '-'"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">PIC</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.pic || '-'"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small"></label>Unit Penugasan</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary"
                                            x-text="selectedPenugasan?.unit_penugasan || '-'"></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Lokasi Penugasan</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.lokasi_penugasan || '-'"></p>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-muted small">Nomor SPRINT</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.nomor_sprint || '-'"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Tanggal Mulai</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                        <i class="bi bi-calendar3 text-muted me-1"></i>
                                        <span x-text="selectedPenugasan?.tanggal_mulai_formatted || '-'"></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Tanggal Selesai</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                        <i class="bi bi-calendar-check text-muted me-1"></i>
                                        <span x-text="selectedPenugasan?.tanggal_selesai_formatted || '-'"></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Durasi Penugasan</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                        <i class="bi bi-hourglass-split text-muted me-1"></i>
                                        <span x-text="selectedPenugasan?.durasi_penugasan || '-'"></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Status</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                        <template x-if="selectedPenugasan?.is_overdue">
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger badge-overdue">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                <span x-text="selectedPenugasan?.sisa_hari"></span>
                                            </span>
                                        </template>
                                        <template x-if="!selectedPenugasan?.is_overdue && selectedPenugasan?.is_warning">
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning border border-warning badge-warning-penugasan">
                                                <i class="bi bi-exclamation-circle-fill me-1"></i>
                                                <span x-text="selectedPenugasan?.sisa_hari"></span>
                                            </span>
                                        </template>
                                        <template
                                            x-if="!selectedPenugasan?.is_overdue && !selectedPenugasan?.is_warning && selectedPenugasan?.tanggal_selesai">
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                <span x-text="selectedPenugasan?.sisa_hari"></span>
                                            </span>
                                        </template>
                                        <template x-if="!selectedPenugasan?.tanggal_selesai">
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                                Belum diatur
                                            </span>
                                        </template>
                                    </p>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-muted small">Keterangan</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                        x-text="selectedPenugasan?.keterangan || '-'"></p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Tutup
                            </button>
                            @can('admin')
                                <a x-show="selectedPenugasan"
                                    :href="selectedPenugasan ? `/data-penugasan/${selectedPenugasan.id}/edit` : '#'"
                                    class="btn btn-warning">
                                    <i class="bi bi-pencil-fill me-1"></i>Edit Data
                                </a>
                                <button type="button" x-show="selectedPenugasan" class="btn btn-danger"
                                    @click="confirmDelete(selectedPenugasan.id)">
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Daftar Karyawan Penugasan Overdue
                            </h1>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger mb-3">
                                <strong>Perhatian!</strong> {{ $overdueCount }} karyawan berikut sudah melewati tanggal
                                selesai
                                penugasan dan memerlukan tindakan segera.
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
                                        @forelse($overdueList as $index => $penugasan)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><span class="badge bg-dark">{{ $penugasan->nik }}</span></td>
                                                <td><strong>{{ $penugasan->nama }}</strong></td>
                                                <td>{{ $penugasan->unit_penugasan }}</td>
                                                <td>{{ $penugasan->lokasi_penugasan }}</td>
                                                <td>{{ $penugasan->tanggal_selesai_formatted }}</td>
                                                <td>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                        {{ $penugasan->sisa_hari }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                            @click="switchToDetail({{ $penugasan->id }})"
                                                            title="Lihat Detail">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                        @can('admin')
                                                            <a href="{{ route('data-penugasan.edit', $penugasan->id) }}"
                                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                                <i class="bi bi-pencil-fill"></i>
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">Tidak ada data overdue.
                                                </td>
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
                                <i class="bi bi-exclamation-circle-fill me-2"></i>Daftar Karyawan Penugasan Mendekati Batas
                                Waktu
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning mb-3">
                                <strong>Peringatan!</strong> {{ $warningCount }} karyawan berikut akan menyelesaikan masa
                                penugasan
                                dalam 15 hari ke depan.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="table-warning">
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Unit Penugasan</th>
                                            <th>Lokasi Penugasan</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($warningList as $index => $penugasan)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><span class="badge bg-dark">{{ $penugasan->nik }}</span></td>
                                                <td><strong>{{ $penugasan->nama }}</strong></td>
                                                <td>{{ $penugasan->unit_penugasan }}</td>
                                                <td>{{ $penugasan->lokasi_penugasan }}</td>
                                                <td>{{ $penugasan->tanggal_selesai_formatted }}</td>
                                                <td>
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-exclamation-circle-fill me-1"></i>
                                                        {{ $penugasan->sisa_hari }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                            @click="switchToDetail({{ $penugasan->id }})"
                                                            title="Lihat Detail">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                        @can('admin')
                                                            <a href="{{ route('data-penugasan.edit', $penugasan->id) }}"
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
    </div>
    </div>
@endsection

@push('body-scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('penugasanTable', (initialData = []) => ({
                dataPenugasan: initialData,
                searchTerm: '',
                sortColumn: 'created_at',
                sortDirection: 'desc',
                itemsPerPage: 10,
                currentPage: 1,
                deleteUrl: '',
                selectedPenugasan: null,

                get filteredData() {
                    if (this.searchTerm === '') return this.dataPenugasan;
                    const term = this.searchTerm.toLowerCase();
                    return this.dataPenugasan.filter(penugasan =>
                        Object.values(penugasan).some(value =>
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

                showPenugasanDetail(penugasan) {
                    this.selectedPenugasan = penugasan;
                    const modal = new bootstrap.Modal(document.getElementById('penugasanDetailModal'));
                    modal.show();
                },

                switchToDetail(penugasanId) {
                    // Find the Penugasan data
                    const penugasan = this.dataPenugasan.find(p => p.id === penugasanId);
                    if (!penugasan) return;

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
                        this.showPenugasanDetail(penugasan);
                    }, 400);
                },

                confirmDelete(penugasanId) {
                    // Set delete URL
                    this.deleteUrl = `/data-penugasan/${penugasanId}`;

                    // Close detail modal first
                    const detailModal = bootstrap.Modal.getInstance(document.getElementById(
                        'penugasanDetailModal'));
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
