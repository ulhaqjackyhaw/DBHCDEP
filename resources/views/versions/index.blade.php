@extends('layouts.app')

@section('title', 'History Versi Data')
@section('header-title', 'History Snapshot Version Data')

@section('content')
    {{-- Notifikasi --}}
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

    {{-- DITAMBAHKAN: Form Filter Tanggal --}}
    <div class="bg-white p-4 sm:p-5 rounded-xl shadow-sm mb-4">
        <h2 class="text-lg font-semibold text-slate-800 mb-3">Filter Berdasarkan Tanggal</h2>
        <form action="{{ route('versions.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="year" class="form-label">Tahun</label>
                <select name="year" id="year" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ ($filters['year'] ?? '') == $year ? 'selected' : '' }}>
                            {{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="month" class="form-label">Bulan</label>
                <select name="month" id="month" class="form-select">
                    <option value="">Semua Bulan</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ ($filters['month'] ?? '') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label for="day" class="form-label">Tanggal Spesifik</label>
                <input type="date" name="day" id="day" class="form-control"
                    value="{{ $filters['day'] ?? '' }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ route('versions.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>


    <div class="bg-white p-4 sm:p-5 rounded-xl shadow-sm" x-data="versionActions()">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-lg font-semibold text-slate-800 mb-0">Daftar Versi Tersimpan</h2>
            <div class="d-flex gap-2">
                @can('admin')
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                        data-bs-target="#createSnapshotModal">
                        <i class="bi bi-camera-fill"></i>
                        <span>Buat Snapshot Baru</span>
                    </button>
                @endcan
                <a href="{{ route('karyawan.index') }}" class="btn btn-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Data Karyawan</span>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-slate-500 font-semibold">No</th>
                        <th class="text-slate-500 font-semibold">Deskripsi / Catatan</th>
                        <th class="text-slate-500 font-semibold">Jumlah Data</th>
                        <th class="text-slate-500 font-semibold">Tanggal Disimpan (WIB)</th>
                        <th class="text-slate-500 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($versions as $version)
                        <tr class="text-slate-700">
                            <td>{{ $loop->iteration + ($versions->currentPage() - 1) * $versions->perPage() }}</td>
                            <td>{{ $version->description }}</td>
                            <td>{{ $version->history_count }} Karyawan</td>
                            <td>{{ $version->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if ($version->history_count > 0)
                                        <a href="{{ route('versions.download', $version->id) }}"
                                            class="btn btn-sm btn-outline-primary" title="Download Excel">
                                            <i class="bi bi-download"></i> Excel
                                        </a>
                                    @endif

                                    @can('admin')
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Pulihkan"
                                            data-bs-toggle="modal" data-bs-target="#confirmationModal"
                                            @click="setupRestore({{ $version->id }}, '{{ e($version->description) }}')">
                                            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                            data-bs-toggle="modal" data-bs-target="#confirmationModal"
                                            @click="setupDelete({{ $version->id }}, '{{ e($version->description) }}')">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    @else
                                        <span class="badge bg-secondary"
                                            title="Hanya Admin yang dapat memulihkan dan menghapus versi">
                                            <i class="bi bi-lock-fill"></i> Akses Terbatas
                                        </span>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                @if (collect($filters)->filter()->isNotEmpty())
                                    Tidak ada versi data yang ditemukan untuk filter yang dipilih.
                                @else
                                    Belum ada versi data yang disimpan.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $versions->links() }}
        </div>

        <!-- Modal Buat Snapshot Baru -->
        <div class="modal fade" id="createSnapshotModal" tabindex="-1" aria-labelledby="createSnapshotLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('versions.store') }}" method="POST">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h1 class="modal-title fs-5" id="createSnapshotLabel">
                                <i class="bi bi-camera-fill me-2"></i> Buat Snapshot Baru
                            </h1>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">
                                    Deskripsi / Catatan <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" id="description" class="form-control" rows="3" required
                                    placeholder="Contoh: Backup sebelum update data bulan Januari 2025"></textarea>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Berikan deskripsi yang jelas untuk memudahkan
                                    identifikasi snapshot ini di kemudian hari.
                                </div>
                            </div>
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                <strong>Snapshot akan dibuat untuk:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Semua data karyawan aktif saat ini</li>
                                    <li>Waktu snapshot: {{ now()->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Buat Snapshot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Dinamis -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalLabel">
                            <i :class="modalIcon"></i> <span x-text="modalTitle"></span>
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p x-html="modalMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form :action="formAction" method="POST">
                            @csrf
                            <template x-if="isDelete">
                                @method('DELETE')
                            </template>
                            <button type="submit" :class="confirmButtonClass" x-text="confirmButtonText"></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('body-scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('versionActions', () => ({
                modalTitle: '',
                modalMessage: '',
                modalIcon: '',
                formAction: '',
                isDelete: false,
                confirmButtonClass: 'btn',
                confirmButtonText: '',

                setupRestore(versionId, description) {
                    this.modalTitle = 'Konfirmasi Pulihkan Data';
                    this.modalMessage =
                        `Anda yakin ingin memulihkan data ke versi <strong>"${description}"</strong>?<br><br>Semua data karyawan saat ini akan diganti.`;
                    this.modalIcon = 'bi bi-arrow-counterclockwise text-success me-2';
                    this.formAction = `/versions/${versionId}/restore`;
                    this.isDelete = false;
                    this.confirmButtonClass = 'btn btn-success';
                    this.confirmButtonText = 'Ya, Pulihkan';
                },

                setupDelete(versionId, description) {
                    this.modalTitle = 'Konfirmasi Hapus Versi';
                    this.modalMessage =
                        `Anda yakin ingin menghapus permanen versi <strong>"${description}"</strong>?<br><br>Proses ini tidak dapat diurungkan.`;
                    this.modalIcon = 'bi bi-exclamation-triangle-fill text-danger me-2';
                    this.formAction = `/versions/${versionId}`;
                    this.isDelete = true;
                    this.confirmButtonClass = 'btn btn-danger';
                    this.confirmButtonText = 'Ya, Hapus';
                }
            }));
        });
    </script>
@endpush
