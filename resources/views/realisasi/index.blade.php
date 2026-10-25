@extends('layouts.app')

@section('title', 'Tabel Realisasi')
@section('header-title', 'Tabel Realisasi ' . ($selectedYear ?? date('Y')))

{{-- CSS yang sudah diperbaiki untuk tampilan responsif --}}
@push('head-scripts')
    <style>
        /* ===== GLOBAL & BASE STYLES ===== */
        #realisasi-page * {
            box-sizing: border-box;
        }

        #realisasi-page {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Fluid Typography */
        #realisasi-page .btn {
            font-size: clamp(0.75rem, 1.5vw, 0.875rem);
            padding: clamp(0.35rem, 1vw, 0.5rem) clamp(0.65rem, 2vw, 1rem);
            white-space: nowrap;
        }

        #realisasi-page .form-control,
        #realisasi-page .form-select {
            font-size: clamp(0.75rem, 1.5vw, 0.875rem);
        }

        #realisasi-page .small {
            font-size: clamp(0.7rem, 1.3vw, 0.875rem) !important;
        }

        /* Prevent overflow */
        #realisasi-page .d-flex {
            max-width: 100%;
        }

        #realisasi-page .flex-wrap {
            flex-wrap: wrap !important;
        }

        /* Button groups */
        #realisasi-page .btn-group {
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        /* Bagian 1: Gaya dasar tabel Bootstrap */
        #realisasi-page .table {
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

        #realisasi-page .table> :not(caption)>*>* {
            padding: 0.4rem 0.5rem;
            background-color: var(--bs-table-bg);
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
            font-size: clamp(0.75rem, 1.5vw, 0.875rem);
        }

        #realisasi-page .table>thead {
            vertical-align: bottom;
        }

        #realisasi-page .table-hover>tbody>tr:hover>* {
            --bs-table-accent-bg: var(--bs-table-hover-bg);
        }

        #realisasi-page .align-middle {
            vertical-align: middle !important;
        }

        /* ===== MOBILE (<576px) ===== */
        @media (max-width: 575.98px) {
            #realisasi-page .p-3 {
                padding: 0.75rem !important;
            }

            #realisasi-page .mb-3 {
                margin-bottom: 0.75rem !important;
            }

            #realisasi-page .gap-2 {
                gap: 0.35rem !important;
            }

            #realisasi-page .btn {
                font-size: 0.7rem !important;
                padding: 0.35rem 0.5rem !important;
            }

            #realisasi-page .btn i {
                font-size: 0.75rem;
            }

            #realisasi-page .btn .text-nowrap {
                display: none;
            }

            #realisasi-page .form-control,
            #realisasi-page .form-select {
                font-size: 0.75rem !important;
                padding: 0.35rem 0.5rem !important;
            }

            #realisasi-page .form-label {
                font-size: 0.7rem !important;
            }

            /* Stack all controls vertically */
            #realisasi-page .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
            }

            #realisasi-page .d-flex.gap-2 {
                width: 100%;
            }

            #realisasi-page input[type="text"] {
                width: 100% !important;
            }

            /* Pagination */
            #realisasi-page .pagination {
                font-size: 0.7rem;
                gap: 0.25rem;
            }

            #realisasi-page .page-link {
                padding: 0.35rem 0.5rem;
            }

            /* Card mode untuk tabel */
            #realisasi-page .table thead {
                display: none;
            }

            #realisasi-page .table tr {
                display: block;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                margin-bottom: 0.75rem;
                overflow: hidden;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }

            #realisasi-page .table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.65rem 0.75rem !important;
                text-align: right !important;
                border: none;
                border-bottom: 1px solid #f1f5f9;
                word-break: break-word;
                font-size: 0.75rem !important;
            }

            #realisasi-page .table td:last-child {
                border-bottom: none;
            }

            #realisasi-page .table td:before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
                padding-right: 0.75rem;
                color: #334155;
                flex-shrink: 0;
                font-size: 0.7rem;
            }

            #realisasi-page .table td .d-flex {
                justify-content: flex-end !important;
            }
        }

        /* ===== TABLET (576-767px) ===== */
        @media (min-width: 576px) and (max-width: 767.98px) {
            #realisasi-page .p-3 {
                padding: 1rem !important;
            }

            #realisasi-page .btn {
                font-size: 0.75rem !important;
                padding: 0.4rem 0.6rem !important;
            }

            #realisasi-page .form-control,
            #realisasi-page .form-select {
                font-size: 0.8rem !important;
            }

            /* Normal table dengan horizontal scroll */
            #realisasi-page .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            #realisasi-page .table> :not(caption)>*>* {
                padding: 0.4rem 0.5rem;
                font-size: 0.75rem;
                white-space: nowrap;
            }

            #realisasi-page .table thead th {
                font-size: 0.7rem !important;
            }

            #realisasi-page .table tbody td {
                font-size: 0.75rem !important;
            }
        }

        /* ===== iPAD (768-1024px) ===== */
        @media (min-width: 768px) and (max-width: 1024px) {
            #realisasi-page .p-3 {
                padding: 1.25rem !important;
            }

            #realisasi-page .btn {
                font-size: 0.8rem !important;
                padding: 0.45rem 0.7rem !important;
            }

            #realisasi-page .form-control,
            #realisasi-page .form-select {
                font-size: 0.85rem !important;
            }

            /* Normal table dengan horizontal scroll */
            #realisasi-page .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            #realisasi-page .table> :not(caption)>*>* {
                padding: 0.4rem 0.5rem;
                font-size: 0.8rem;
                white-space: nowrap;
            }

            #realisasi-page .table thead th {
                font-size: 0.75rem !important;
            }

            #realisasi-page .table tbody td {
                font-size: 0.8rem !important;
            }

            /* Reduce column widths */
            #realisasi-page input[type="text"] {
                max-width: 180px !important;
            }

            /* Wrap controls jika perlu */
            #realisasi-page .d-flex.justify-content-between {
                gap: 0.75rem;
            }
        }

        /* ===== MacBook 13" (1280-1440px) ===== */
        @media (min-width: 1280px) and (max-width: 1440px) {
            #realisasi-page .p-3 {
                padding: 1.5rem !important;
            }

            #realisasi-page .btn {
                font-size: 0.82rem !important;
                padding: 0.45rem 0.75rem !important;
            }

            #realisasi-page .form-control,
            #realisasi-page .form-select {
                font-size: 0.85rem !important;
            }

            #realisasi-page input[type="text"] {
                max-width: 220px !important;
            }

            /* Normal table dengan horizontal scroll */
            #realisasi-page .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            #realisasi-page .table> :not(caption)>*>* {
                padding: 0.4rem 0.5rem;
                font-size: 0.82rem;
                white-space: nowrap;
            }

            #realisasi-page .table thead th {
                font-size: 0.75rem !important;
            }

            #realisasi-page .table tbody td {
                font-size: 0.82rem !important;
            }

            /* Button layout optimization */
            #realisasi-page .d-flex.gap-2 .btn span {
                display: inline;
            }
        }

        /* ===== DESKTOP (>1440px) ===== */
        @media (min-width: 1441px) {

            /* Normal table view */
            #realisasi-page .table thead {
                display: table-header-group;
            }

            #realisasi-page .table tr {
                display: table-row;
            }

            #realisasi-page .table td {
                display: table-cell;
            }

            #realisasi-page .table td:before {
                content: none;
            }
        }

        /* ===== MODAL RESPONSIVENESS ===== */
        @media (max-width: 575.98px) {
            #realisasi-page .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }

            #realisasi-page .modal-body {
                padding: 1rem;
                font-size: 0.85rem;
            }

            #realisasi-page .modal-header,
            #realisasi-page .modal-footer {
                padding: 0.75rem 1rem;
            }

            #realisasi-page .modal-title {
                font-size: 1rem !important;
            }

            #realisasi-page .alert ol {
                font-size: 0.75rem;
                padding-left: 1.25rem;
            }
        }

        /* ===== TOUCH DEVICE OPTIMIZATION ===== */
        @media (hover: none) and (pointer: coarse) {
            #realisasi-page .btn {
                min-height: 44px;
                min-width: 44px;
            }

            #realisasi-page .form-control,
            #realisasi-page .form-select {
                min-height: 44px;
            }

            #realisasi-page .page-link {
                min-width: 44px;
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        /* ===== PRINT STYLES ===== */
        @media print {

            #realisasi-page .btn,
            #realisasi-page .alert,
            #realisasi-page .pagination {
                display: none !important;
            }

            #realisasi-page .table {
                font-size: 0.7rem;
            }
        }
    </style>
@endpush


@section('content')
    {{-- 1. Tambahkan div pembungkus dengan ID unik --}}
    <div id="realisasi-page">

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

        <div class="bg-white rounded-xl shadow-sm" x-data="realisasiTable({{ $items->toJson() ?? '[]' }}, {{ (int) ($selectedYear ?? date('Y')) }})">
            <div class="p-3">
                {{-- Header + Aksi --}}
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        @can('admin')
                            <a href="{{ route('realisasi.create', ['tahun' => $selectedYear ?? date('Y')]) }}"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                                <i class="bi bi-plus-circle-fill"></i>
                                <span class="text-nowrap">Tambah Data</span>
                            </a>

                            {{-- Dropdown untuk Import (Admin Only) --}}
                            <div class="btn-group" role="group">
                                <button type="button"
                                    class="btn btn-warning btn-sm dropdown-toggle d-flex align-items-center gap-2"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-upload"></i>
                                    <span class="text-nowrap">Import</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('realisasi.template', ['tahun' => $selectedYear ?? date('Y')]) }}">
                                            <i class="bi bi-file-earmark-arrow-down me-2"></i>Download Template
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target="#importModal">
                                            <i class="bi bi-upload me-2"></i>Import Data
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endcan

                        {{-- Tombol Export (Semua User) --}}
                        <a href="{{ route('realisasi.export', ['tahun' => $selectedYear ?? date('Y')]) }}"
                            class="btn btn-success btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            <span class="text-nowrap">Download Data Realisasi</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <form method="GET" action="{{ route('realisasi.index') }}"
                            class="d-flex align-items-center gap-2">
                            <label for="tahun" class="form-label mb-0 text-slate-600 small">Tahun</label>
                            <select id="tahun" name="tahun" class="form-select form-select-sm" style="width:auto;">
                                @php($current = $selectedYear ?? date('Y'))
                                @foreach ($years ?? collect([$current]) as $year)
                                    <option value="{{ $year }}"
                                        {{ (int) $year === (int) $current ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
                        </form>
                        <div class="d-flex align-items-center gap-2">
                            <label for="itemsPerPage"
                                class="form-label text-nowrap mb-0 text-slate-600 small">Tampilkan</label>
                            <select id="itemsPerPage" class="form-select form-select-sm" style="width: auto;"
                                x-model.number="itemsPerPage">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="1000">1000</option>
                            </select>
                            <span class="text-slate-600 text-nowrap small">data</span>
                        </div>
                        <div style="width: 200px;">
                            <input type="text" class="form-control form-control-sm" placeholder="Cari realisasi..."
                                x-model.debounce.300ms="searchTerm">
                        </div>
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-slate-500 font-semibold text-nowrap small">No</th>
                                <th @click="sortBy('program_kerja')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap small">
                                    Program Kerja <i :class="sortIcon('program_kerja')"></i>
                                </th>
                                <th @click="sortBy('rkap')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap text-end small">
                                    RKAP {{ $selectedYear ?? date('Y') }} <i :class="sortIcon('rkap')"></i>
                                </th>
                                <th @click="sortBy('realisasi_jan_mar')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap text-end small">
                                    Realisasi Jan–Mar <i :class="sortIcon('realisasi_jan_mar')"></i>
                                </th>
                                <th @click="sortBy('realisasi_jan_jun')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap text-end small">
                                    Realisasi Jan–Jun <i :class="sortIcon('realisasi_jan_jun')"></i>
                                </th>
                                <th @click="sortBy('realisasi_jul_sep')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap text-end small">
                                    Realisasi Jul–Sep <i :class="sortIcon('realisasi_jul_sep')"></i>
                                </th>
                                <th @click="sortBy('realisasi_jul_des')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap text-end small">
                                    Realisasi Jul–Des <i :class="sortIcon('realisasi_jul_des')"></i>
                                </th>
                                <th @click="sortBy('total')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap text-end small">
                                    Total <i :class="sortIcon('total')"></i>
                                </th>
                                <th @click="sortBy('ach_s1')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap text-end small">
                                    Achievement S1 <i :class="sortIcon('ach_s1')"></i>
                                </th>
                                <th @click="sortBy('ach_s2')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap text-end small">
                                    Achievement S2 <i :class="sortIcon('ach_s2')"></i>
                                </th>
                                <th @click="sortBy('ach_year')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap text-end small">
                                    Achievement {{ $selectedYear ?? date('Y') }} <i :class="sortIcon('ach_year')"></i>
                                </th>
                                @can('admin')
                                    <th class="text-slate-500 font-semibold text-nowrap small">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in paginatedItems" :key="item.id">
                                <tr class="text-slate-700">
                                    <td data-label="No" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                    <td data-label="Program Kerja" class="text-wrap" style="max-width: 320px;"
                                        x-text="item.program_kerja"></td>
                                    <td data-label="RKAP {{ $selectedYear ?? date('Y') }}" class="text-end"
                                        x-text="num(item.rkap)"></td>
                                    <td data-label="Realisasi Jan–Mar" class="text-end"
                                        x-text="num(item.realisasi_jan_mar)"></td>
                                    <td data-label="Realisasi Jan–Jun" class="text-end"
                                        x-text="num(item.realisasi_jan_jun)"></td>
                                    <td data-label="Realisasi Jul–Sep" class="text-end"
                                        x-text="num(item.realisasi_jul_sep)"></td>
                                    <td data-label="Realisasi Jul–Des" class="text-end"
                                        x-text="num(item.realisasi_jul_des)"></td>
                                    <td data-label="Total" class="text-end" x-text="num(item.total)"></td>
                                    <td data-label="Achievement S1" class="text-end" x-text="pct(achS1(item))"></td>
                                    <td data-label="Achievement S2" class="text-end" x-text="pct(achS2(item))"></td>
                                    <td data-label="Achievement {{ $selectedYear ?? date('Y') }}" class="text-end"
                                        x-text="pct(achYear(item))"></td>
                                    @can('admin')
                                        <td data-label="Aksi">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <a :href="`/realisasi/${item.id}/edit?tahun=${selectedYear}`"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                    data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                                    @click="deleteUrl = `/realisasi/${item.id}`">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            </template>
                            <tr x-show="!paginatedItems.length">
                                @php($isAdmin = auth()->check() && auth()->user()->can('admin'))
                                <td colspan="{{ $isAdmin ? 12 : 11 }}" class="text-center text-muted py-5">
                                    <span x-show="items.length > 0">Data tidak ditemukan.</span>
                                    <span x-show="items.length === 0">Belum ada data realisasi.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <div class="text-slate-600 small">
                        Menampilkan
                        <span x-text="Math.min((currentPage - 1) * itemsPerPage + 1, sortedItems.length)"></span>
                        sampai
                        <span x-text="Math.min(currentPage * itemsPerPage, sortedItems.length)"></span>
                        dari <span x-text="sortedItems.length"></span> data
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

            {{-- Modal Konfirmasi Hapus --}}
            @can('admin')
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
                                Apakah Anda benar-benar yakin ingin menghapus data realisasi ini? Proses ini tidak dapat
                                diurungkan.
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
            @endcan

            {{-- Modal Import --}}
            @can('admin')
                <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('realisasi.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="importModalLabel">
                                        <i class="bi bi-upload me-2 text-success"></i>Import Data Realisasi
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Petunjuk:</strong>
                                        <ol class="mb-0 mt-2">
                                            <li>Download template Excel terlebih dahulu</li>
                                            <li>Isi data sesuai format template</li>
                                            <li>Upload file Excel yang sudah diisi</li>
                                            <li>Data yang sudah ada akan diupdate berdasarkan Program Kerja</li>
                                        </ol>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Mode Import <span class="text-danger">*</span></label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="import_mode" id="mode_add"
                                                value="add" checked>
                                            <label class="form-check-label" for="mode_add">
                                                <strong>Mode Tambah</strong>
                                                <div class="text-muted small">Data baru akan ditambahkan ke database. Data lama
                                                    tetap ada dan tidak akan terhapus.</div>
                                            </label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="import_mode"
                                                id="mode_replace" value="replace">
                                            <label class="form-check-label" for="mode_replace">
                                                <strong>Mode Ganti Semua</strong>
                                                <div class="text-muted small">Semua data lama akan dihapus dan diganti dengan
                                                    data dari file Excel yang baru.</div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="import_tahun" class="form-label">Tahun <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="import_tahun" name="tahun"
                                            value="{{ $selectedYear ?? date('Y') }}" min="2000" max="2100" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="import_file" class="form-label">File Excel <span
                                                class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="import_file" name="file"
                                            accept=".xlsx,.xls,.csv" required>
                                        <div class="form-text">Format: .xlsx, .xls, .csv (Maksimal 5MB)</div>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <strong>Kesalahan:</strong>
                                            <ul class="mb-0 mt-2">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-upload me-2"></i>Import Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

    </div> {{-- Penutup untuk div #realisasi-page --}}
@endsection

@push('body-scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('realisasiTable', (initialItems = [], initialYear = new Date().getFullYear()) => ({
                items: initialItems,
                selectedYear: initialYear,
                searchTerm: '',
                sortColumn: 'created_at',
                sortDirection: 'desc',
                itemsPerPage: 10,
                currentPage: 1,
                deleteUrl: '',

                // Helpers
                num(value) {
                    const n = Number(value ?? 0);
                    return n.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                pct(value) {
                    const v = Number(value ?? 0) * 100;
                    return `${v.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%`;
                },
                achS1(item) {
                    const rkap = Number(item.rkap ?? 0);
                    if (!rkap) return 0;
                    return Number(item.realisasi_jan_jun ?? 0) / rkap;
                },
                achS2(item) {
                    const rkap = Number(item.rkap ?? 0);
                    if (!rkap) return 0;
                    return Number(item.realisasi_jul_des ?? 0) / rkap;
                },
                achYear(item) {
                    const rkap = Number(item.rkap ?? 0);
                    if (!rkap) return 0;
                    return (Number(item.realisasi_jan_jun ?? 0) + Number(item.realisasi_jul_des ?? 0)) /
                        rkap;
                },
                getValue(item, column) {
                    switch (column) {
                        case 'ach_s1':
                            return this.achS1(item);
                        case 'ach_s2':
                            return this.achS2(item);
                        case 'ach_year':
                            return this.achYear(item);
                        default:
                            return item[column];
                    }
                },

                // Filtering
                get filteredItems() {
                    if (this.searchTerm === '') return this.items;
                    const term = this.searchTerm.toLowerCase();
                    return this.items.filter(item =>
                        Object.values(item).some(value => String(value ?? '').toLowerCase()
                            .includes(term))
                    );
                },

                // Sorting
                get sortedItems() {
                    return [...this.filteredItems].sort((a, b) => {
                        const colA = this.getValue(a, this.sortColumn);
                        const colB = this.getValue(b, this.sortColumn);

                        if (colA == null && colB == null) return 0;
                        if (colA == null) return this.sortDirection === 'asc' ? -1 : 1;
                        if (colB == null) return this.sortDirection === 'asc' ? 1 : -1;

                        const aNum = Number(colA);
                        const bNum = Number(colB);
                        let comparison;
                        if (!Number.isNaN(aNum) && !Number.isNaN(bNum)) {
                            comparison = aNum - bNum;
                        } else {
                            comparison = String(colA).localeCompare(String(colB));
                        }
                        return this.sortDirection === 'asc' ? comparison : -comparison;
                    });
                },

                // Pagination
                get paginatedItems() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.sortedItems.slice(start, end);
                },
                get totalPages() {
                    return Math.ceil(this.sortedItems.length / this.itemsPerPage) || 1;
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
                    if (current < 4) end = 5;
                    if (current > total - 3) start = total - 4;
                    if (start > 2) pagesArray.push('...');
                    for (let i = start; i <= end; i++) pagesArray.push(i);
                    if (end < total - 1) pagesArray.push('...');
                    pagesArray.push(total);
                    return pagesArray;
                },
                changePage(page) {
                    if (page < 1 || page > this.totalPages) return;
                    this.currentPage = page;
                    document.querySelector('main')?.scrollTo(0, 0);
                },

                // UI helpers
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

                init() {
                    this.$watch('searchTerm', () => this.currentPage = 1);
                    this.$watch('itemsPerPage', () => this.currentPage = 1);
                }
            }));
        });
    </script>
@endpush
