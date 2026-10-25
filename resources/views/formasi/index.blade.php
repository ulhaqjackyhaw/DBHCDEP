@extends('layouts.app')

@section('title', 'Data Formasi')
@section('header-title', 'Data Formasi')

@push('head-styles')
    <style>
        /* Container Size - Full width responsive */
        .formasi-container {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            max-height: none !important;
            overflow: visible;
            margin: 0;
            padding: 0 0.5rem;
            box-sizing: border-box;
        }

        .formasi-container>* {
            max-width: 100%;
        }

        /* Compact table styling - Sangat kecil */
        .table-compact {
            font-size: 0.65rem;
            /* Diperkecil dari 0.75rem */
            margin-bottom: 0;
            table-layout: auto;
            width: 100%;
        }

        .table-compact th,
        .table-compact td {
            padding: 0.25rem 0.35rem;
            /* Diperkecil dari 0.35rem 0.5rem */
            white-space: nowrap;
            vertical-align: middle;
            line-height: 1.2;
        }

        /* Kolom No - sangat kecil */
        .table-compact th:nth-child(1),
        .table-compact td:nth-child(1) {
            width: 35px;
            max-width: 35px;
            text-align: center;
            padding: 0.25rem 0.2rem;
        }

        /* Kode Jabatan */
        .table-compact th:nth-child(2),
        .table-compact td:nth-child(2) {
            width: 110px;
            max-width: 110px;
            white-space: normal;
            word-wrap: break-word;
            font-size: 0.6rem;
        }

        /* Unit Deputy EGM */
        .table-compact th:nth-child(3),
        .table-compact td:nth-child(3) {
            width: 90px;
            max-width: 90px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Unit Assistant Deputy */
        .table-compact th:nth-child(4),
        .table-compact td:nth-child(4) {
            width: 90px;
            max-width: 90px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Unit Division Head */
        .table-compact th:nth-child(5),
        .table-compact td:nth-child(5) {
            width: 90px;
            max-width: 90px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Unit Department Head */
        .table-compact th:nth-child(6),
        .table-compact td:nth-child(6) {
            width: 90px;
            max-width: 90px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Lokasi Kerja */
        .table-compact th:nth-child(7),
        .table-compact td:nth-child(7) {
            width: 85px;
            max-width: 85px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Unit Kerja */
        .table-compact th:nth-child(8),
        .table-compact td:nth-child(8) {
            width: 100px;
            max-width: 100px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Jabatan */
        .table-compact th:nth-child(9),
        .table-compact td:nth-child(9) {
            width: 120px;
            max-width: 120px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* KKJ */
        .table-compact th:nth-child(10),
        .table-compact td:nth-child(10) {
            width: 50px;
            max-width: 50px;
            text-align: center;
        }

        /* Grade */
        .table-compact th:nth-child(11),
        .table-compact td:nth-child(11) {
            width: 45px;
            max-width: 45px;
            text-align: center;
        }

        /* Kuota */
        .table-compact th:nth-child(12),
        .table-compact td:nth-child(12) {
            width: 50px;
            max-width: 50px;
            text-align: center;
        }

        /* Kolom Aksi */
        .table-compact th:last-child,
        .table-compact td:last-child {
            width: 85px;
            max-width: 85px;
            white-space: nowrap;
            text-align: center;
            padding: 0.25rem 0.2rem;
        }


        /* Table responsive wrapper */
        .table-responsive {
            transform-origin: top left;
            transition: transform 0.3s ease;
            border-radius: 0.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
        }

        /* Zoom table untuk fit screen */
        @media (min-width: 1024px) and (max-width: 1600px) {
            .table-responsive {
                zoom: 0.85;
            }
        }

        @media (min-width: 768px) and (max-width: 1023px) {
            .table-responsive {
                zoom: 0.75;
            }
        }

        /* Scrollbar styling */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Badge sizing - lebih kecil */
        .table-compact .badge {
            font-size: 0.6rem;
            padding: 0.2rem 0.4rem;
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Button sizing in table - lebih kecil */
        .table-compact .btn-sm {
            padding: 0.2rem 0.3rem;
            font-size: 0.65rem;
        }

        .table-compact .btn-sm i {
            font-size: 0.65rem;
        }

        /* Header text size */
        .table-compact thead th {
            font-size: 0.65rem;
            font-weight: 600;
        }

        /* Responsive untuk layar kecil */
        @media (max-width: 1400px) {
            .table-compact {
                font-size: 0.6rem;
            }

            .table-compact th,
            .table-compact td {
                padding: 0.2rem 0.3rem;
            }
        }

        @media (max-width: 1200px) {
            .table-compact {
                font-size: 0.55rem;
            }

            .table-compact th,
            .table-compact td {
                padding: 0.15rem 0.25rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="formasi-container">
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
                    <a href="{{ route('formasi.template.download') }}"
                        class="btn btn-outline-success d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                        <span>Download Template</span>
                    </a>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <form action="{{ route('formasi.import.add') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex gap-3">
                            @csrf
                            <input type="file" name="file" class="form-control" required>
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 text-nowrap"><i
                                    class="bi bi-cloud-arrow-up-fill"></i> Tambah</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('formasi.import.replace') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex gap-3">
                            @csrf
                            <input type="file" name="file" class="form-control" required>
                            <button type="submit" class="btn btn-danger d-flex align-items-center gap-2 text-nowrap"><i
                                    class="bi bi-arrow-repeat"></i> Ganti Semua</button>
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
                                    <small>Semua data lama akan dihapus dan diganti dengan data dari file Excel yang
                                        baru.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        {{-- Tabel Data --}}
        <div class="bg-white rounded-xl shadow-sm p-3" x-data="formasiTable({{ $formasi->toJson() ?? '[]' }})">
            <div class="p-3">
                {{-- Header Tabel --}}
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        @can('admin')
                            <a href="{{ route('formasi.create') }}"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                                <i class="bi bi-plus-circle-fill"></i>
                                <span class="text-nowrap">Tambah Data</span>
                            </a>
                        @endcan
                        <a href="{{ route('formasi.export') }}"
                            class="btn btn-success btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            <span class="text-nowrap">Download Data Excel</span>
                        </a>
                    </div>
                    {{-- Sisi Kanan: Filter dan Search --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
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
                            <input type="text" class="form-control form-control-sm" placeholder="Cari formasi..."
                                x-model.debounce.300ms="searchTerm">
                        </div>
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-compact">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-slate-500 font-semibold text-nowrap small">No</th>
                                <th @click="sortBy('kode_jabatan')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none small"
                                    style="min-width: 110px;">
                                    Kode Jabatan <i :class="sortIcon('kode_jabatan')"></i></th>
                                <th @click="sortBy('unit_deputy_egm')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none small"
                                    style="min-width: 90px;">
                                    Deputy EGM <i :class="sortIcon('unit_deputy_egm')"></i></th>
                                <th @click="sortBy('unit_assistant_deputy')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none small"
                                    style="min-width: 90px;">
                                    Asst Deputy <i :class="sortIcon('unit_assistant_deputy')"></i></th>
                                <th @click="sortBy('unit_division_head')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none small"
                                    style="min-width: 90px;">
                                    Div Head <i :class="sortIcon('unit_division_head')"></i></th>
                                <th @click="sortBy('unit_department_head')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none small"
                                    style="min-width: 90px;">
                                    Dept Head <i :class="sortIcon('unit_department_head')"></i></th>
                                <th @click="sortBy('lokasi_kerja')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none small"
                                    style="min-width: 85px;">
                                    Lokasi <i :class="sortIcon('lokasi_kerja')"></i></th>
                                <th @click="sortBy('unit_kerja')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none small"
                                    style="min-width: 100px;">
                                    Unit Kerja <i :class="sortIcon('unit_kerja')"></i>
                                </th>
                                <th @click="sortBy('jabatan')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none small"
                                    style="min-width: 120px;">
                                    Jabatan <i :class="sortIcon('jabatan')"></i>
                                </th>
                                <th @click="sortBy('kelompok_kelas_jabatan')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap small">
                                    KKJ <i :class="sortIcon('kelompok_kelas_jabatan')"></i></th>
                                <th @click="sortBy('grade')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap small">
                                    Grade <i :class="sortIcon('grade')"></i>
                                </th>
                                <th @click="sortBy('kuota')"
                                    class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap small">
                                    Kuota <i :class="sortIcon('kuota')"></i>
                                </th>

                                @can(abilities: 'admin')
                                    <th class="text-slate-500 font-semibold text-nowrap small">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in paginatedFormasi" :key="item.id">
                                <tr class="text-slate-700">
                                    <td x-text="(currentPage - 1) * itemsPerPage + index + 1" class="text-center"></td>
                                    <td x-text="item.kode_jabatan" style="font-size: 0.85rem;"></td>
                                    <td x-text="item.unit_deputy_egm || '-'" style="font-size: 0.85rem;"></td>
                                    <td x-text="item.unit_assistant_deputy || '-'" style="font-size: 0.85rem;"></td>
                                    <td x-text="item.unit_division_head || '-'" style="font-size: 0.85rem;"></td>
                                    <td x-text="item.unit_department_head || '-'" style="font-size: 0.85rem;"></td>
                                    <td x-text="item.lokasi_kerja || '-'" style="font-size: 0.85rem;"></td>
                                    <td x-text="item.unit_kerja || '-'" style="font-size: 0.85rem;"></td>
                                    <td x-text="item.jabatan" style="font-size: 0.85rem;"></td>
                                    <td x-text="item.kelompok_kelas_jabatan" class="text-center"
                                        style="font-size: 0.85rem;"></td>
                                    <td x-text="item.grade" class="text-center" style="font-size: 0.85rem;"></td>
                                    <td x-text="item.kuota" style="font-size: 1rem;"></span></td>
                                    @can(abilities: 'admin')
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a :href="`/formasi/${item.id}/edit`" class="btn btn-sm btn-outline-warning"
                                                    title="Edit" style="padding: 0.15rem 0.3rem;">
                                                    <i class="bi bi-pencil-fill" style="font-size: 0.65rem;"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                    data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                                    @click="deleteUrl = `/formasi/${item.id}`"
                                                    style="padding: 0.15rem 0.3rem;">
                                                    <i class="bi bi-trash-fill" style="font-size: 0.65rem;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            </template>
                            <tr x-show="!paginatedFormasi.length">
                                <td colspan="12" class="text-center text-muted py-5">
                                    <span x-show="formasi.length > 0">Data tidak ditemukan.</span>
                                    <span x-show="formasi.length === 0">Belum ada data formasi.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <div class="text-slate-600 small">
                        Menampilkan <span
                            x-text="Math.min((currentPage - 1) * itemsPerPage + 1, sortedFormasi.length)"></span>
                        sampai <span x-text="Math.min(currentPage * itemsPerPage, sortedFormasi.length)"></span>
                        dari <span x-text="sortedFormasi.length"></span> data
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

            <!-- Modal Konfirmasi Hapus -->
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
                            Apakah Anda benar-benar yakin ingin menghapus data formasi ini? Proses ini tidak dapat
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
        </div>
    </div> {{-- End formasi-container --}}
@endsection

@push('body-scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formasiTable', (initialFormasi = []) => ({
                formasi: initialFormasi,
                searchTerm: '',
                sortColumn: 'created_at',
                sortDirection: 'desc',
                itemsPerPage: 10,
                currentPage: 1,
                deleteUrl: '',

                get filteredFormasi() {
                    if (this.searchTerm === '') return this.formasi;
                    const term = this.searchTerm.toLowerCase();
                    return this.formasi.filter(item =>
                        Object.values(item).some(value =>
                            String(value).toLowerCase().includes(term)
                        )
                    );
                },
                get sortedFormasi() {
                    return [...this.filteredFormasi].sort((a, b) => {
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
                get paginatedFormasi() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.sortedFormasi.slice(start, end);
                },
                get totalPages() {
                    return Math.ceil(this.sortedFormasi.length / this.itemsPerPage);
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
                init() {
                    this.$watch('searchTerm', () => this.currentPage = 1);
                    this.$watch('itemsPerPage', () => this.currentPage = 1);
                }
            }));
        });
    </script>
@endpush
