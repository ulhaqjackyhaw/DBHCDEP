@extends('layouts.app')

@section('title', 'Daftar Jabatan Lowong')
@section('header-title', 'Daftar Jabatan Lowong')

@push('head-scripts')
    {{-- CSS styling for consistency and responsiveness --}}
    <style>
        :root {
            --primary-color: #4f46e5;
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-color-dark: #1e293b;
            --text-color-light: #64748b;
            --border-color: #e2e8f0;
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-color-dark);
            margin-bottom: 1rem;
        }

        .table thead th {
            background: #f1f5f9;
            border-bottom: 2px solid var(--border-color);
            color: var(--text-color-light);
            font-weight: 600;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .pagination-container nav {
            display: flex;
            justify-content: center;
        }

        @media (max-width: 576px) {
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }

            .pagination .page-item {
                margin-bottom: 0.5rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Card Import & Export --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="card-title">Import & Export Data</h6>
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <form action="{{ route('lowong.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="file" class="form-label small">Upload file Excel untuk mengganti semua data yang ada:</label>
                        <div class="input-group">
                            <input type="file" class="form-control" name="file" id="file" required>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-upload me-2"></i>Import</button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('lowong.template') }}" class="btn btn-outline-success w-100"><i class="bi bi-download me-2"></i>Download Template</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js-powered Table Card --}}
    <div class="card" x-data="lowongTable({{ $lowongs->toJson() }})">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label for="itemsPerPage" class="form-label text-nowrap mb-0 text-muted">Tampilkan</label>
                    <select id="itemsPerPage" class="form-select form-select-sm" style="width: auto;" x-model.number="itemsPerPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="flex-grow-1" style="max-width: 300px;">
                    <input type="text" class="form-control" placeholder="Cari jabatan, nama, lokasi..." x-model.debounce.300ms="searchTerm">
                </div>
                <a href="{{ route('lowong.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Tambah Manual</a>
            </div>

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger">
                <h6 class="alert-heading fw-bold">Terjadi Masalah!</h6>
                <p>{{ session('error') }}</p>
                @if (session('import_errors'))
                <hr>
                <ul class="mb-0">
                    @foreach (session('import_errors') as $error)
                    <li><small>{{ $error }}</small></li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-nowrap">No.</th>
                            <th @click="sortBy('jabatan')" class="cursor-pointer user-select-none text-nowrap">Jabatan <i :class="sortIcon('jabatan')"></i></th>
                            <th @click="sortBy('nama')" class="cursor-pointer user-select-none text-nowrap">Nama <i :class="sortIcon('nama')"></i></th>
                            <th @click="sortBy('status')" class="cursor-pointer user-select-none text-nowrap">Status <i :class="sortIcon('status')"></i></th>
                            <th @click="sortBy('nik')" class="cursor-pointer user-select-none text-nowrap">NIK/Lowong <i :class="sortIcon('nik')"></i></th>
                            <th @click="sortBy('lokasi')" class="cursor-pointer user-select-none text-nowrap">Lokasi <i :class="sortIcon('lokasi')"></i></th>
                            <th @click="sortBy('level')" class="cursor-pointer user-select-none text-nowrap">Level <i :class="sortIcon('level')"></i></th>
                            <th @click="sortBy('tipe')" class="cursor-pointer user-select-none text-nowrap">Tipe <i :class="sortIcon('tipe')"></i></th>
                            <th @click="sortBy('kj_api')" class="cursor-pointer user-select-none text-nowrap">KJ API <i :class="sortIcon('kj_api')"></i></th>
                            <th style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(lowong, index) in paginatedData" :key="lowong.id">
                            {{-- Baris akan berwarna biru muda jika lowong --}}
                            <tr :class="{ 'table-primary': lowong.is_vacant }">
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td x-text="lowong.jabatan"></td>
                                <td x-text="lowong.nama"></td>
                                <td x-text="lowong.status"></td>
                                <td x-text="lowong.nik"></td>
                                <td x-text="lowong.lokasi"></td>
                                <td x-text="lowong.level"></td>
                                <td x-text="lowong.tipe"></td>
                                <td><span class="badge bg-info" x-text="lowong.kj_api"></span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form :action="`{{ url('lowong') }}/${lowong.id}/toggle-status`" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" :class="lowong.is_vacant ? 'btn-outline-primary' : 'btn-primary'">
                                                <i class="bi" :class="lowong.is_vacant ? 'bi-person-check-fill' : 'bi-person-dash-fill'"></i>
                                                <span x-text="lowong.is_vacant ? 'Terisi' : 'Kosong'"></span>
                                            </button>
                                        </form>
                                        <a :href="`{{ url('lowong') }}/${lowong.id}/edit`" class="btn btn-sm btn-warning">Edit</a>
                                        <form :action="`{{ url('lowong') }}/${lowong.id}`" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!paginatedData.length">
                            <td colspan="10" class="text-center text-muted py-4">
                                <span x-show="allData.length > 0">Data tidak ditemukan.</span>
                                <span x-show="allData.length === 0">Belum ada data jabatan lowong.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 gap-3">
                <div class="text-muted small">
                    Menampilkan <span x-text="Math.min((currentPage - 1) * itemsPerPage + 1, sortedData.length)"></span>
                    sampai <span x-text="Math.min(currentPage * itemsPerPage, sortedData.length)"></span>
                    dari <span x-text="sortedData.length"></span> data
                </div>
                <nav x-show="totalPages > 1" class="pagination-container">
                    <ul class="pagination mb-0">
                        <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                            <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">Previous</a>
                        </li>
                        <template x-for="page in pages">
                            <li class="page-item" :class="{ 'active': page === currentPage, 'disabled': page === '...' }">
                                <a class="page-link" href="#" @click.prevent="if (page !== '...') changePage(page)" x-text="page"></a>
                            </li>
                        </template>
                        <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                            <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@push('body-scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('lowongTable', (initialData = []) => ({
            allData: initialData,
            searchTerm: '',
            sortColumn: 'jabatan',
            sortDirection: 'asc',
            itemsPerPage: 10,
            currentPage: 1,

            get filteredData() {
                if (this.searchTerm === '') return this.allData;
                const term = this.searchTerm.toLowerCase();
                return this.allData.filter(item =>
                    Object.values(item).some(value =>
                        String(value).toLowerCase().includes(term)
                    )
                );
            },

            get sortedData() {
                return [...this.filteredData].sort((a, b) => {
                    // Prioritas pertama: urutkan berdasarkan is_vacant (nilai 1 di atas)
                    const vacantComparison = (b.is_vacant || 0) - (a.is_vacant || 0);
                    if (vacantComparison !== 0) {
                        return vacantComparison;
                    }

                    // Prioritas kedua: jika is_vacant sama, gunakan kolom yg dipilih user
                    const colA = a[this.sortColumn];
                    const colB = b[this.sortColumn];
                    let comparison = 0;

                    // Gunakan localeCompare untuk sorting string yang lebih akurat
                    if (typeof colA === 'string' && typeof colB === 'string') {
                        comparison = colA.localeCompare(colB, undefined, {
                            numeric: true,
                            sensitivity: 'base'
                        });
                    } else {
                        if (colA > colB) comparison = 1;
                        else if (colA < colB) comparison = -1;
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
                const maxPages = 7,
                    total = this.totalPages,
                    current = this.currentPage;
                if (total <= maxPages) return Array.from({
                    length: total
                }, (_, i) => i + 1);

                const pagesArray = [1];
                let start = Math.max(2, current - 2),
                    end = Math.min(total - 1, current + 2);

                if (current < 4) end = 5;
                if (current > total - 3) start = total - 4;

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
                // Scroll to top of the main content area after page change
                this.$root.querySelector('main').scrollTo(0, 0);
            },

            init() {
                this.$watch('searchTerm', () => this.currentPage = 1);
                this.$watch('itemsPerPage', () => this.currentPage = 1);
            }
        }));
    });
</script>
@endpush