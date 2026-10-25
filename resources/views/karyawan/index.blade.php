@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('header-title', 'Data Karyawan')

@push('head-styles')
    <style>
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

        .table-responsive {
            border-radius: 0.5rem;
            overflow-x: auto;
        }

        .btn-outline-info:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        /* Compact table styling */
        .table-compact {
            font-size: 0.875rem;
        }

        .table-compact th,
        .table-compact td {
            padding: 0.5rem 0.75rem;
            white-space: nowrap;
            vertical-align: middle;
        }

        /* Set max width untuk kolom tertentu */
        .table-compact td:nth-child(3) {
            /* Nama */
            max-width: 200px;
            white-space: normal;
        }

        /* Kolom aksi tetap kecil */
        .table-compact td:last-child,
        .table-compact td:nth-last-child(2) {
            width: 1%;
            white-space: nowrap;
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

    @can('admin')
        {{-- Bagian Upload Massal --}}
        <div class="bg-white p-4 sm:p-5 rounded-xl shadow-sm mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h2 class="text-lg font-semibold text-slate-800 mb-0">Upload Data Massal</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('karyawan.template.download') }}"
                        class="btn btn-outline-success d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                        <span>Download Template</span>
                    </a>

                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <form id="importAddForm" action="{{ route('karyawan.import.add') }}" method="POST"
                        enctype="multipart/form-data" class="d-flex gap-3">
                        @csrf
                        <input type="file" name="file" class="form-control" required>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 text-nowrap"><i
                                class="bi bi-cloud-arrow-up-fill"></i> Tambah</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form id="importReplaceForm" action="{{ route('karyawan.import.replace') }}" method="POST"
                        enctype="multipart/form-data" class="d-flex gap-3">
                        @csrf
                        <input type="file" name="file" class="form-control" required>
                        <button type="submit" class="btn btn-danger d-flex align-items-center gap-2 text-nowrap"><i
                                class="bi bi-arrow-repeat"></i> Ganti Semua</button>
                    </form>
                </div>
            </div>
        @endcan


        {{-- Penjelasan Mode Upload --}}
        @can('admin')
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
                                <small>Semua data lama akan dihapus dan diganti dengan data dari file Excel yang baru.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan


    {{-- Tabel Data --}}
    <div class="bg-white rounded-xl shadow-sm" x-data="employeeTable({{ $employees->toJson() ?? '[]' }})">
        <div class="p-4 sm:p-5">
            {{-- Header Tabel --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @can('admin')
                        <a href="{{ route('karyawan.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span class="text-nowrap">Tambah Data</span>
                        </a>
                    @endcan
                    @can('admin')
                        <a href="{{ route('versions.index') }}" class="btn btn-outline-info d-flex align-items-center gap-2">
                            <i class="bi bi-archive-fill"></i>
                            <span class="text-nowrap">History Versi Data</span>
                        </a>
                    @endcan
                    <a href="{{ route('karyawan.export') }}" class="btn btn-success d-flex align-items-center gap-2">
                        <i class="bi bi-download"></i>
                        <span>Download Data Saat Ini</span>
                    </a>
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
                            <option value="1000">1000</option>
                        </select>
                        <span class="text-slate-600 text-nowrap">data</span>
                    </div>
                    <div style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Cari karyawan..."
                            x-model.debounce.300ms="searchTerm">
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle table-compact">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-slate-500 font-semibold text-nowrap">No</th>
                            <th @click="sortBy('nik')"
                                class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">NIK <i
                                    :class="sortIcon('nik')"></i></th>
                            <th @click="sortBy('nama')"
                                class="text-slate-500 font-semibold cursor-pointer user-select-none">Nama <i
                                    :class="sortIcon('nama')"></i></th>
                            <th @click="sortBy('kode_jabatan')"
                                class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Kode
                                Jabatan <i :class="sortIcon('kode_jabatan')"></i></th>
                            <th @click="sortBy('lokasi_kerja')"
                                class="text-slate-500 font-semibold cursor-pointer user-select-none">Lokasi <i
                                    :class="sortIcon('lokasi_kerja')"></i></th>
                            <th @click="sortBy('unit_kerja')"
                                class="text-slate-500 font-semibold cursor-pointer user-select-none">Unit <i
                                    :class="sortIcon('unit_kerja')"></i></th>
                            <th @click="sortBy('jabatan')"
                                class="text-slate-500 font-semibold cursor-pointer user-select-none">Jabatan <i
                                    :class="sortIcon('jabatan')"></i></th>
                            <th @click="sortBy('kelompok_kelas_jabatan')"
                                class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">KKJ <i
                                    :class="sortIcon('kelompok_kelas_jabatan')"></i></th>
                            <th @click="sortBy('sub_status')"
                                class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Status <i
                                    :class="sortIcon('sub_status')"></i></th>
                            <th class="text-slate-500 font-semibold text-nowrap">Detail</th>
                            @can(abilities: 'admin')
                                <th class="text-slate-500 font-semibold text-nowrap">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(employee, index) in paginatedEmployees" :key="employee.id">
                            <tr class="text-slate-700 cursor-pointer" @click="showEmployeeDetail(employee)"
                                title="Klik untuk melihat detail">
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1" style="font-size: 0.85rem;">
                                </td>
                                <td x-text="employee.nik" style="font-size: 0.85rem;"></td>
                                <td x-text="employee.nama" style="font-size: 0.85rem;"></td>
                                <td x-text="employee.kode_jabatan" style="font-size: 0.85rem;"></td>
                                <td x-text="employee.lokasi_kerja" style="font-size: 0.85rem;"></td>
                                <td x-text="employee.unit_kerja" style="font-size: 0.85rem;"></td>
                                <td x-text="employee.jabatan" style="font-size: 0.85rem;"></td>
                                <td x-text="employee.kelompok_kelas_jabatan" style="font-size: 0.85rem;"></td>
                                <td><span class="badge bg-info-subtle text-info-emphasis rounded-pill"
                                        x-text="employee.sub_status"></span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info" title="Lihat Detail"
                                        @click.stop="showEmployeeDetail(employee)">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </td>
                                @can('admin')
                                    <td>
                                        <div class="d-flex gap-2" @click.stop>
                                            <a :href="`/data-karyawan/${employee.id}/edit`"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                                @click="deleteUrl = `/data-karyawan/${employee.id}`">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan

                            </tr>
                        </template>
                        <tr x-show="!paginatedEmployees.length">
                            <td colspan="11" class="text-center text-muted py-5">
                                <span x-show="employees.length > 0">Data tidak ditemukan.</span>
                                <span x-show="employees.length === 0">Belum ada data karyawan.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                <div class="text-slate-600">
                    Menampilkan <span
                        x-text="Math.min((currentPage - 1) * itemsPerPage + 1, sortedEmployees.length)"></span>
                    sampai <span x-text="Math.min(currentPage * itemsPerPage, sortedEmployees.length)"></span>
                    dari <span x-text="sortedEmployees.length"></span> data
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

        <!-- Modal Detail Karyawan -->
        <div class="modal fade" id="employeeDetailModal" tabindex="-1" aria-labelledby="employeeDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h1 class="modal-title fs-5" id="employeeDetailModalLabel">
                            <i class="bi bi-person-fill me-2"></i>Detail Data Karyawan
                        </h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" x-show="selectedEmployee" style="max-height: 70vh; overflow-y: auto;">
                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs mb-3" id="employeeDetailTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab"
                                    data-bs-target="#basic" type="button" role="tab">
                                    <i class="bi bi-person-badge me-1"></i>Info Dasar
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="unit-tab" data-bs-toggle="tab" data-bs-target="#unit"
                                    type="button" role="tab">
                                    <i class="bi bi-diagram-3 me-1"></i>Unit & Struktur
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="job-tab" data-bs-toggle="tab" data-bs-target="#job"
                                    type="button" role="tab">
                                    <i class="bi bi-briefcase me-1"></i>Info Jabatan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="personal-tab" data-bs-toggle="tab"
                                    data-bs-target="#personal" type="button" role="tab">
                                    <i class="bi bi-person-lines-fill me-1"></i>Data Personal
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="career-tab" data-bs-toggle="tab" data-bs-target="#career"
                                    type="button" role="tab">
                                    <i class="bi bi-graph-up-arrow me-1"></i>Karir & Klasifikasi
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="license-tab" data-bs-toggle="tab" data-bs-target="#license"
                                    type="button" role="tab">
                                    <i class="bi bi-award me-1"></i>Lisensi
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                                    type="button" role="tab">
                                    <i class="bi bi-telephone me-1"></i>Kontak
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history"
                                    type="button" role="tab">
                                    <i class="bi bi-clock-history me-1"></i>Riwayat
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="employeeDetailTabContent">
                            <!-- Tab 1: Info Dasar -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">NIK</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.nik || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Nama</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.nama || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Jenis Kelamin</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.jenis_kelamin || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Tanggal Lahir</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.tanggal_lahir || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Usia</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill"
                                                x-text="selectedEmployee ? calculateAge(selectedEmployee.tanggal_lahir) + ' tahun' : '-'"></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Status Kepegawaian</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill"
                                                x-text="selectedEmployee?.sub_status || '-'"></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Asal Instansi</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.asal_instansi || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Instansi</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.instansi || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Pendidikan Diakui</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.pendidikan_diakui || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Pendidikan Dimiliki</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.pendidikan_dimiliki || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">TMT Karyawan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.tmt_karyawan || selectedEmployee?.tmt || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Masa Kerja</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill"
                                                x-text="selectedEmployee?.masa_kerja || (selectedEmployee ? calculateWorkPeriod(selectedEmployee.tmt) + ' tahun' : '-')"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Unit & Struktur -->
                            <div class="tab-pane fade" id="unit" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Unit Deputy EGM</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.unit_deputy_egm || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Unit Assistant
                                            Deputy</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.unit_assistant_deputy || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Unit Division Head</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.unit_division_head || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Unit Department Head</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.unit_department_head || '-'"></p>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold text-muted small">Unit Kerja</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.unit_kerja || '-'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Info Jabatan -->
                            <div class="tab-pane fade" id="job" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Kode Jabatan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.kode_jabatan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Jabatan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.jabatan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">TMT Jabatan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.tmt_jabatan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Status Jabatan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.status_jabatan || '-'"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted small">Job Grade</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill"
                                                x-text="selectedEmployee?.job_grade || selectedEmployee?.grade || '-'"></span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted small">Person Grade</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill"
                                                x-text="selectedEmployee?.person_grade || '-'"></span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted small">Kelompok Kelas
                                            Jabatan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.kelompok_kelas_jabatan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Lokasi Kerja</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.lokasi_kerja || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Awal Lokasi Kerja</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.awal_lokasi_kerja || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Sub Status</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.sub_status || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Fungsi Kontrak OS</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.fungsi_kontrak_os || '-'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 4: Data Personal -->
                            <div class="tab-pane fade" id="personal" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Agama</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.agama || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Status Pernikahan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.status_pernikahan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Jurusan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.jurusan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Generasi</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.generasi || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">No KTP</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.no_ktp || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">No Kontrak</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.no_kontrak || '-'"></p>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold text-muted small">Alamat KTP</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.alamat_ktp || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Rencana MPP</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.rencana_mpp || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Rencana Pensiun</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.rencana_pensiun || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Kategori</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.kategori || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Nilai NPI 2022</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.nilai_npi_2022 || '-'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 5: Karir & Klasifikasi -->
                            <div class="tab-pane fade" id="career" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Keluarga Jabatan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.keluarga_jabatan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Sub Keluarga Jabatan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.sub_keluarga_jabatan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Fungsi Jabatan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.fungsi_jabatan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Fungsi Pekerjaan</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.fungsi_pekerjaan || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Jalur Karir</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.jalur_karir || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Jenjang Karir</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.jenjang_karir || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">TMT KJ Tertinggi</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.tmt_kj_tertinggi || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Masa KJ Tertinggi
                                            (Tahun)</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.masa_kj_tertinggi_tahun || '-'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 6: Lisensi -->
                            <div class="tab-pane fade" id="license" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold text-muted small">Lisensi Dimiliki</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.lisence_dimiliki || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Rating</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill"
                                                x-text="selectedEmployee?.rating || '-'"></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">No STKP</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.no_stkp || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Masa Berlaku</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.masa_berlaku || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Lisensi Dibayarkan
                                            Januari</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.lisence_dibayarkan_januari || '-'"></p>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold text-muted small">Cek Lisensi/Serkom</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.cek_lisence_serkom || '-'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 7: Kontak -->
                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Email</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                            <a :href="selectedEmployee?.email ? 'mailto:' + selectedEmployee.email : '#'"
                                                x-text="selectedEmployee?.email || '-'"></a>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">No HP</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                            <a :href="selectedEmployee?.no_hp ? 'tel:' + selectedEmployee.no_hp : '#'"
                                                x-text="selectedEmployee?.no_hp || '-'"></a>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 8: Riwayat & Performa -->
                            <div class="tab-pane fade" id="history" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">No SK Jabatan
                                            Terakhir</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.no_sk_jabatan_terakhir || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Tgl SK Jabatan
                                            Terakhir</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.tgl_sk_jabatan_terakhir || '-'"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">KPI 2023</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill"
                                                x-text="selectedEmployee?.kpi_2023 || '-'"></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted small">Kriteria</label>
                                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                            x-text="selectedEmployee?.kriteria || '-'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Tutup
                        </button>
                        @can(abilities: 'admin')
                            <a x-show="selectedEmployee"
                                :href="selectedEmployee ? `/data-karyawan/${selectedEmployee.id}/edit` : '#'"
                                class="btn btn-warning">
                                <i class="bi bi-pencil-fill me-1"></i>Edit Data
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Delete Confirmation -->
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
    </div>

    <div class="modal fade" id="versioningModal" tabindex="-1" aria-labelledby="versioningModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="versioningModalLabel">Simpan Versi Data Karyawan?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('versions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Anda baru saja mengubah data. Simpan kondisi data saat ini sebagai "save point" yang bisa
                            dipulihkan nanti?</p>
                        <div class="mb-3">
                            <label for="versionDescription" class="form-label">Deskripsi / Catatan Perubahan
                                (Opsional)</label>
                            {{-- PERBAIKAN: Atribut 'required' dihapus dari sini --}}
                            <input type="text" class="form-control" id="versionDescription" name="description"
                                placeholder="Contoh: Setelah impor data September">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Lewati</button>
                        <button type="submit" class="btn btn-primary">Ya, Simpan Versi Ini</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('body-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- SEMUA LOGIKA UNTUK MODAL ADA DI SINI ---

            const successMessage = "{{ session('success') }}";
            const versioningModalElement = document.getElementById('versioningModal');

            if (versioningModalElement) {
                const versioningModal = new bootstrap.Modal(versioningModalElement);
                const form = versioningModalElement.querySelector('form');

                // Tampilkan modal jika ada notif sukses (dan bukan dari fitur versi itu sendiri)
                if (successMessage && !successMessage.includes('Versi data') && !successMessage.includes(
                        'dipulihkan')) {
                    versioningModal.show();
                }

                // Fungsi untuk handle tombol Enter
                const handleEnterKey = (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault(); // Mencegah aksi default
                        form.submit(); // Submit form di dalam modal
                    }
                };

                // Aktifkan listener saat modal ditampilkan
                versioningModalElement.addEventListener('shown.bs.modal', () => {
                    versioningModalElement.querySelector('input[name="description"]').focus();
                    document.addEventListener('keydown', handleEnterKey);
                });

                // Matikan listener saat modal ditutup
                versioningModalElement.addEventListener('hidden.bs.modal', () => {
                    document.removeEventListener('keydown', handleEnterKey);
                });
            }
        });

        // --- KODE ALPINE.JS ANDA TETAP UTUH DI BAWAH INI ---
        document.addEventListener('alpine:init', () => {
            Alpine.data('employeeTable', (initialEmployees = []) => ({
                employees: initialEmployees,
                searchTerm: '',
                sortColumn: 'created_at',
                sortDirection: 'desc',
                itemsPerPage: 10,
                currentPage: 1,
                deleteUrl: '',
                selectedEmployee: null,

                get filteredEmployees() {
                    if (this.searchTerm === '') return this.employees;
                    const term = this.searchTerm.toLowerCase();
                    return this.employees.filter(emp =>
                        Object.values(emp).some(value =>
                            String(value).toLowerCase().includes(term)
                        )
                    );
                },
                get sortedEmployees() {
                    return [...this.filteredEmployees].sort((a, b) => {
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
                get paginatedEmployees() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.sortedEmployees.slice(start, end);
                },
                get totalPages() {
                    return Math.ceil(this.sortedEmployees.length / this.itemsPerPage);
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
                // Fungsi untuk menghitung usia berdasarkan tanggal lahir
                calculateAge(birthDate) {
                    if (!birthDate) return 0;

                    // Parse berbagai format tanggal (dd/mm/yyyy, dd-mm-yyyy, yyyy-mm-dd)
                    let parsedDate;
                    if (birthDate.includes('/')) {
                        const parts = birthDate.split('/');
                        parsedDate = new Date(parts[2], parts[1] - 1, parts[0]); // dd/mm/yyyy
                    } else if (birthDate.includes('-')) {
                        if (birthDate.split('-')[0].length === 4) {
                            parsedDate = new Date(birthDate); // yyyy-mm-dd
                        } else {
                            const parts = birthDate.split('-');
                            parsedDate = new Date(parts[2], parts[1] - 1, parts[0]); // dd-mm-yyyy
                        }
                    } else {
                        return 0;
                    }

                    const today = new Date();
                    const birthYear = parsedDate.getFullYear();
                    const birthMonth = parsedDate.getMonth();
                    const birthDay = parsedDate.getDate();

                    let age = today.getFullYear() - birthYear;

                    // Jika belum ulang tahun tahun ini, kurangi 1
                    if (today.getMonth() < birthMonth ||
                        (today.getMonth() === birthMonth && today.getDate() < birthDay)) {
                        age--;
                    }

                    return Math.max(0, age);
                },
                // Fungsi untuk menghitung masa kerja berdasarkan TMT
                calculateWorkPeriod(tmtDate) {
                    if (!tmtDate) return 0;

                    // Parse berbagai format tanggal (dd/mm/yyyy, dd-mm-yyyy, yyyy-mm-dd)
                    let parsedDate;
                    if (tmtDate.includes('/')) {
                        const parts = tmtDate.split('/');
                        parsedDate = new Date(parts[2], parts[1] - 1, parts[0]); // dd/mm/yyyy
                    } else if (tmtDate.includes('-')) {
                        if (tmtDate.split('-')[0].length === 4) {
                            parsedDate = new Date(tmtDate); // yyyy-mm-dd
                        } else {
                            const parts = tmtDate.split('-');
                            parsedDate = new Date(parts[2], parts[1] - 1, parts[0]); // dd-mm-yyyy
                        }
                    } else {
                        return 0;
                    }

                    const today = new Date();
                    const tmtYear = parsedDate.getFullYear();
                    const tmtMonth = parsedDate.getMonth();
                    const tmtDay = parsedDate.getDate();

                    let workYears = today.getFullYear() - tmtYear;

                    // Jika belum mencapai anniversary tahun ini, kurangi 1
                    if (today.getMonth() < tmtMonth ||
                        (today.getMonth() === tmtMonth && today.getDate() < tmtDay)) {
                        workYears--;
                    }

                    return Math.max(0, workYears);
                },
                showEmployeeDetail(employee) {
                    this.selectedEmployee = employee;
                    const modal = new bootstrap.Modal(document.getElementById('employeeDetailModal'));
                    modal.show();
                },
                init() {
                    this.$watch('searchTerm', () => this.currentPage = 1);
                    this.$watch('itemsPerPage', () => this.currentPage = 1);
                }
            }));
        });
    </script>
@endpush
