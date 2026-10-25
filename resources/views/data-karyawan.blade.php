@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('header-title', 'Data Karyawan')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ... kode lainnya ... --}}
    <div class="bg-white p-4 sm:p-5 rounded-xl shadow-sm mb-5">
        {{-- DIUBAH: Menambahkan wrapper div untuk judul dan tombol download --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="text-lg font-semibold text-slate-800 mb-0">Upload Data Massal</h2>
            {{-- DITAMBAHKAN: Tombol untuk download template --}}
            <a href="{{ route('karyawan.template.download') }}" class="btn btn-outline-success d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-arrow-down-fill"></i>
                <span>Download Template</span>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <form action="{{ route('karyawan.import.add') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-3">
                @csrf
                <input type="file" name="file" class="form-control" required>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 text-nowrap"><i class="bi bi-cloud-arrow-up-fill"></i> Tambah</button>
            </form>
            <form action="{{ route('karyawan.import.replace') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-3">
                @csrf
                <input type="file" name="file" class="form-control" required>
                <button type="submit" class="btn btn-danger d-flex align-items-center gap-2 text-nowrap"><i class="bi bi-arrow-repeat"></i> Ganti Semua</button>
            </form>
        </div>
    </div>

    {{-- DIUBAH: Menghapus event listener .window dan menambahkan input search yang selalu terlihat --}}
    <div class="bg-white rounded-xl shadow-sm" x-data="employeeTable({{ $employees->toJson() ?? '[]' }})">
        <div class="p-4 sm:p-5">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label for="itemsPerPage" class="form-label text-nowrap mb-0 text-slate-600">Tampilkan</label>
                    <select id="itemsPerPage" class="form-select form-select-sm" style="width: auto;" x-model.number="itemsPerPage">
                        <option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
                    </select>
                    <span class="text-slate-600 text-nowrap">data</span>
                </div>
                
                <div class="d-none d-md-block" style="width: 250px;">
                    <input type="text" class="form-control" placeholder="Cari karyawan..." x-model.debounce.300ms="searchTerm">
                </div>
                
                <div class="w-100 d-md-none">
                    <input type="text" class="form-control" placeholder="Cari karyawan..." x-model.debounce.300ms="searchTerm">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-slate-500 font-semibold text-nowrap">No</th>
                            <th @click="sortBy('NIK')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">NIK <i :class="sortIcon('NIK')"></i></th>
                            <th @click="sortBy('Nama')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Nama <i :class="sortIcon('Nama')"></i></th>
                            <th @click="sortBy('GENDER')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Gender <i :class="sortIcon('GENDER')"></i></th>
                            <th @click="sortBy('UNIT')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Unit <i :class="sortIcon('UNIT')"></i></th>
                            <th @click="sortBy('JABATAN')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Jabatan <i :class="sortIcon('JABATAN')"></i></th>
                            <th @click="sortBy('KELOMPOK_KELAS_JABATAN')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">KKJ <i :class="sortIcon('KELOMPOK_KELAS_JABATAN')"></i></th>
                            <th @click="sortBy('GRADE')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Grade <i :class="sortIcon('GRADE')"></i></th>
                            <th @click="sortBy('STATUS_KEPEGAWAIAN')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Status <i :class="sortIcon('STATUS_KEPEGAWAIAN')"></i></th>
                            <th @click="sortBy('USIA')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Usia <i :class="sortIcon('USIA')"></i></th>
                            <th @click="sortBy('PENDIDIKAN')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Pendidikan <i :class="sortIcon('PENDIDIKAN')"></i></th>
                            <th @click="sortBy('MASA_KERJA')" class="text-slate-500 font-semibold cursor-pointer user-select-none text-nowrap">Masa Kerja <i :class="sortIcon('MASA_KERJA')"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(employee, index) in paginatedEmployees" :key="employee.NIK">
                            <tr class="text-slate-700">
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td x-text="employee.NIK"></td>
                                <td x-text="employee.Nama"></td>
                                <td x-text="employee.GENDER"></td>
                                <td x-text="employee.UNIT"></td>
                                <td x-text="employee.JABATAN"></td>
                                <td x-text="employee.KELOMPOK_KELAS_JABATAN"></td>
                                <td><span class="badge bg-primary-subtle text-primary-emphasis rounded-pill" x-text="employee.GRADE"></span></td>
                                <td x-text="employee.STATUS_KEPEGAWAIAN"></td>
                                <td x-text="employee.USIA"></td>
                                <td x-text="employee.PENDIDIKAN"></td>
                                <td x-text="employee.MASA_KERJA"></td>
                            </tr>
                        </template>
                         <tr x-show="!paginatedEmployees.length">
                            <td colspan="12" class="text-center text-muted py-5">
                                <span x-show="employees.length > 0">Data tidak ditemukan.</span>
                                <span x-show="employees.length === 0">Belum ada data karyawan.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                <div class="text-slate-600">
                    Menampilkan <span x-text="Math.min((currentPage - 1) * itemsPerPage + 1, sortedEmployees.length)"></span>
                    sampai <span x-text="Math.min(currentPage * itemsPerPage, sortedEmployees.length)"></span>
                    dari <span x-text="sortedEmployees.length"></span> data
                </div>
                <nav x-show="totalPages > 1">
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
@endsection

@push('body-scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('employeeTable', (initialEmployees = []) => ({
            employees: initialEmployees,
            searchTerm: '',
            sortColumn: 'Nama',
            sortDirection: 'asc',
            itemsPerPage: 10,
            currentPage: 1,
            
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
                    const colA = a[this.sortColumn], colB = b[this.sortColumn]; 
                    let comparison = 0; 
                    if (colA > colB) comparison = 1; 
                    else if (colA < colB) comparison = -1; 
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
                    return Array.from({ length: total }, (_, i) => i + 1);
                }

                const pagesArray = [1];
                let start = Math.max(2, current - 2);
                let end = Math.min(total - 1, current + 2);
                
                if (current < 4) { end = 5; }
                if (current > total - 3) { start = total - 4; }

                if (start > 2) pagesArray.push('...');
                for (let i = start; i <= end; i++) { pagesArray.push(i); }
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