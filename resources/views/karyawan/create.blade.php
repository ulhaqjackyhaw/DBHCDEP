@extends('layouts.app')

@section('title', 'Tambah Data Karyawan')
@section('header-title', 'Tambah Karyawan Baru')

@section('content')
    @push('head-scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    @endpush
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('karyawan.store') }}" method="POST">
                @csrf

                <!-- Nav Tabs -->
                <ul class="nav nav-tabs mb-3" id="createKaryawanTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                            type="button" role="tab">
                            <i class="bi bi-person-badge me-1"></i>Info Dasar *
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="unit-tab" data-bs-toggle="tab" data-bs-target="#unit" type="button"
                            role="tab">
                            <i class="bi bi-diagram-3 me-1"></i>Unit & Struktur
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="job-tab" data-bs-toggle="tab" data-bs-target="#job" type="button"
                            role="tab">
                            <i class="bi bi-briefcase me-1"></i>Info Jabatan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
                            type="button" role="tab">
                            <i class="bi bi-person-lines-fill me-1"></i>Data Personal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="career-tab" data-bs-toggle="tab" data-bs-target="#career"
                            type="button" role="tab">
                            <i class="bi bi-graph-up-arrow me-1"></i>Karir
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
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="createKaryawanTabContent">
                    <!-- Tab 1: Info Dasar (Required Fields) -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Field dengan tanda <strong>*</strong> wajib diisi
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik"
                                    name="nik" value="{{ old('nik') }}" required>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" name="nama" value="{{ old('nama') }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                    id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                    id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="formasi_select" class="form-label">Pilih Formasi (Kode Jabatan)</label>
                                <select class="form-select @error('kode_jabatan') is-invalid @enderror"
                                    id="formasi_select" name="formasi_select" onchange="updateFormasiFields()">
                                    <option value="" disabled selected>Pilih Formasi</option>
                                    @foreach ($formasiList as $formasi)
                                        <option value="{{ $formasi->id }}"
                                            data-kode_jabatan="{{ $formasi->kode_jabatan }}"
                                            data-lokasi_kerja="{{ $formasi->lokasi_kerja }}"
                                            data-unit_kerja="{{ $formasi->unit_kerja }}"
                                            data-jabatan="{{ $formasi->jabatan }}"
                                            data-kkj="{{ $formasi->kelompok_kelas_jabatan }}"
                                            data-grade="{{ $formasi->grade }}"
                                            data-unit_deputy_egm="{{ $formasi->unit_deputy_egm ?? '' }}"
                                            data-unit_assistant_deputy="{{ $formasi->unit_assistant_deputy ?? '' }}"
                                            data-unit_division_head="{{ $formasi->unit_division_head ?? '' }}"
                                            data-unit_department_head="{{ $formasi->unit_department_head ?? '' }}"
                                            {{ old('formasi_select') == $formasi->id ? 'selected' : '' }}>
                                            [{{ $formasi->kode_jabatan }}] {{ $formasi->jabatan }} -
                                            {{ $formasi->unit_kerja }}
                                            ({{ $formasi->lokasi_kerja }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('kode_jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <input type="hidden" id="kode_jabatan" name="kode_jabatan"
                                value="{{ old('kode_jabatan') }}">
                            <input type="hidden" id="jabatan" name="jabatan" value="{{ old('jabatan') }}">
                            <div class="col-md-6 mb-3">
                                <label for="unit_kerja" class="form-label">Unit Kerja</label>
                                <input type="text" class="form-control" id="unit_kerja" name="unit_kerja"
                                    value="{{ old('unit_kerja') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kelompok_kelas_jabatan" class="form-label">Kelompok Kelas Jabatan
                                    (KKJ)</label>
                                <input type="text" class="form-control" id="kelompok_kelas_jabatan"
                                    name="kelompok_kelas_jabatan" value="{{ old('kelompok_kelas_jabatan') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lokasi_kerja" class="form-label">Lokasi Kerja</label>
                                <input type="text" class="form-control" id="lokasi_kerja" name="lokasi_kerja"
                                    value="{{ old('lokasi_kerja') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tmt_karyawan" class="form-label">TMT Karyawan</label>
                                <input type="date" class="form-control @error('tmt_karyawan') is-invalid @enderror"
                                    id="tmt_karyawan" name="tmt_karyawan" value="{{ old('tmt_karyawan') }}">
                                @error('tmt_karyawan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sub_status" class="form-label">Sub Status</label>
                                <select class="form-select @error('sub_status') is-invalid @enderror" id="sub_status"
                                    name="sub_status">
                                    <option value="" disabled selected>Pilih Sub Status</option>
                                    <option value="KP" {{ old('sub_status') == 'KP' ? 'selected' : '' }}>KP</option>
                                    <option value="ALIH DAYA" {{ old('sub_status') == 'ALIH DAYA' ? 'selected' : '' }}>
                                        ALIH DAYA</option>
                                </select>
                                @error('sub_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Unit & Struktur -->
                    <div class="tab-pane fade" id="unit" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unit_deputy_egm" class="form-label">Unit Deputy EGM</label>
                                <input type="text" class="form-control" id="unit_deputy_egm" name="unit_deputy_egm"
                                    value="{{ old('unit_deputy_egm') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unit_assistant_deputy" class="form-label">Unit Assistant Deputy</label>
                                <input type="text" class="form-control" id="unit_assistant_deputy"
                                    name="unit_assistant_deputy" value="{{ old('unit_assistant_deputy') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unit_division_head" class="form-label">Unit Division Head</label>
                                <input type="text" class="form-control" id="unit_division_head"
                                    name="unit_division_head" value="{{ old('unit_division_head') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unit_department_head" class="form-label">Unit Department Head</label>
                                <input type="text" class="form-control" id="unit_department_head"
                                    name="unit_department_head" value="{{ old('unit_department_head') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Info Jabatan -->
                    <div class="tab-pane fade" id="job" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="asal_instansi" class="form-label">Asal Instansi</label>
                                <input type="text" class="form-control @error('asal_instansi') is-invalid @enderror"
                                    id="asal_instansi" name="asal_instansi" value="{{ old('asal_instansi') }}">
                                @error('asal_instansi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status_jabatan" class="form-label">Status Jabatan</label>
                                <select class="form-select" id="status_jabatan" name="status_jabatan">
                                    <option value="" disabled selected>Pilih Status Jabatan</option>
                                    <option value="KARYAWAN" {{ old('status_jabatan') == 'KARYAWAN' ? 'selected' : '' }}>
                                        KARYAWAN</option>
                                    <option value="PEJABAT" {{ old('status_jabatan') == 'PEJABAT' ? 'selected' : '' }}>
                                        PEJABAT</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="person_grade" class="form-label">Person Grade</label>
                                <input type="text" class="form-control" id="person_grade" name="person_grade"
                                    value="{{ old('person_grade') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="awal_lokasi_kerja" class="form-label">Awal Lokasi Kerja</label>
                                <input type="text" class="form-control" id="awal_lokasi_kerja"
                                    name="awal_lokasi_kerja" value="{{ old('awal_lokasi_kerja') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="instansi" class="form-label">Instansi</label>
                                <input type="text" class="form-control" id="instansi" name="instansi"
                                    value="{{ old('instansi') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fungsi_kontrak_os" class="form-label">Fungsi Kontrak OS</label>
                                <input type="text" class="form-control" id="fungsi_kontrak_os"
                                    name="fungsi_kontrak_os" value="{{ old('fungsi_kontrak_os') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="penugasan" class="form-label">Penugasan</label>
                                <input type="text" class="form-control" id="penugasan" name="penugasan"
                                    value="{{ old('penugasan') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Data Personal -->
                    <div class="tab-pane fade" id="personal" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="agama" class="form-label">Agama</label>
                                <select class="form-select" id="agama" name="agama">
                                    <option value="">Pilih Agama</option>
                                    <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen
                                    </option>
                                    <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik
                                    </option>
                                    <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha
                                    </option>
                                    <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status_pernikahan" class="form-label">Status Pernikahan</label>
                                <select class="form-select" id="status_pernikahan" name="status_pernikahan">
                                    <option value="">Pilih Status</option>
                                    <option value="Belum Menikah"
                                        {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah
                                    </option>
                                    <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>
                                        Menikah</option>
                                    <option value="Cerai" {{ old('status_pernikahan') == 'Cerai' ? 'selected' : '' }}>
                                        Cerai</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jurusan" class="form-label">Jurusan</label>
                                <input type="text" class="form-control" id="jurusan" name="jurusan"
                                    value="{{ old('jurusan') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pendidikan_dimiliki" class="form-label">Pendidikan Dimiliki</label>
                                <input type="text" class="form-control" id="pendidikan_dimiliki"
                                    name="pendidikan_dimiliki" value="{{ old('pendidikan_dimiliki') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_ktp" class="form-label">No KTP</label>
                                <input type="text" class="form-control" id="no_ktp" name="no_ktp"
                                    value="{{ old('no_ktp') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="alamat_ktp" class="form-label">Alamat KTP</label>
                                <textarea class="form-control" id="alamat_ktp" name="alamat_ktp" rows="2">{{ old('alamat_ktp') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_kontrak" class="form-label">No Kontrak</label>
                                <input type="text" class="form-control" id="no_kontrak" name="no_kontrak"
                                    value="{{ old('no_kontrak') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="generasi" class="form-label">Generasi</label>
                                <input type="text" class="form-control" id="generasi" name="generasi"
                                    value="{{ old('generasi') }}" placeholder="Misal: Milenial, Gen Z">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rencana_mpp" class="form-label">Rencana MPP</label>
                                <input type="text" class="form-control" id="rencana_mpp" name="rencana_mpp"
                                    value="{{ old('rencana_mpp') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rencana_pensiun" class="form-label">Rencana Pensiun</label>
                                <input type="text" class="form-control" id="rencana_pensiun" name="rencana_pensiun"
                                    value="{{ old('rencana_pensiun') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="masa_kerja" class="form-label">Masa Kerja</label>
                                <input type="text" class="form-control" id="masa_kerja" name="masa_kerja"
                                    value="{{ old('masa_kerja') }}" placeholder="Misal: 10 tahun">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="kategori" name="kategori"
                                    value="{{ old('kategori') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nilai_npi_2022" class="form-label">Nilai NPI 2022</label>
                                <input type="number" step="0.01" class="form-control" id="nilai_npi_2022"
                                    name="nilai_npi_2022" value="{{ old('nilai_npi_2022') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="usia" class="form-label">Usia</label>
                                <input type="number" class="form-control" id="usia" name="usia"
                                    value="{{ old('usia') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Karir & Klasifikasi -->
                    <div class="tab-pane fade" id="career" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="keluarga_jabatan" class="form-label">Keluarga Jabatan</label>
                                <input type="text" class="form-control" id="keluarga_jabatan" name="keluarga_jabatan"
                                    value="{{ old('keluarga_jabatan') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sub_keluarga_jabatan" class="form-label">Sub Keluarga Jabatan</label>
                                <input type="text" class="form-control" id="sub_keluarga_jabatan"
                                    name="sub_keluarga_jabatan" value="{{ old('sub_keluarga_jabatan') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fungsi_jabatan" class="form-label">Fungsi Jabatan</label>
                                <input type="text" class="form-control" id="fungsi_jabatan" name="fungsi_jabatan"
                                    value="{{ old('fungsi_jabatan') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fungsi_pekerjaan" class="form-label">Fungsi Pekerjaan</label>
                                <input type="text" class="form-control" id="fungsi_pekerjaan" name="fungsi_pekerjaan"
                                    value="{{ old('fungsi_pekerjaan') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jalur_karir" class="form-label">Jalur Karir</label>
                                <input type="text" class="form-control" id="jalur_karir" name="jalur_karir"
                                    value="{{ old('jalur_karir') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenjang_karir" class="form-label">Jenjang Karir</label>
                                <input type="text" class="form-control" id="jenjang_karir" name="jenjang_karir"
                                    value="{{ old('jenjang_karir') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tmt_kj_tertinggi" class="form-label">TMT KJ Tertinggi</label>
                                <input type="date" class="form-control" id="tmt_kj_tertinggi" name="tmt_kj_tertinggi"
                                    value="{{ old('tmt_kj_tertinggi') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="masa_kj_tertinggi_tahun" class="form-label">Masa KJ Tertinggi (Tahun)</label>
                                <input type="number" class="form-control" id="masa_kj_tertinggi_tahun"
                                    name="masa_kj_tertinggi_tahun" value="{{ old('masa_kj_tertinggi_tahun') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_sk_jabatan_terakhir" class="form-label">No SK Jabatan Terakhir</label>
                                <input type="text" class="form-control" id="no_sk_jabatan_terakhir"
                                    name="no_sk_jabatan_terakhir" value="{{ old('no_sk_jabatan_terakhir') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tgl_sk_jabatan_terakhir" class="form-label">Tgl SK Jabatan Terakhir</label>
                                <input type="date" class="form-control" id="tgl_sk_jabatan_terakhir"
                                    name="tgl_sk_jabatan_terakhir" value="{{ old('tgl_sk_jabatan_terakhir') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kpi_2023" class="form-label">KPI 2023</label>
                                <input type="number" step="0.01" class="form-control" id="kpi_2023"
                                    name="kpi_2023" value="{{ old('kpi_2023') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kriteria" class="form-label">Kriteria</label>
                                <input type="text" class="form-control" id="kriteria" name="kriteria"
                                    value="{{ old('kriteria') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tab 6: Lisensi -->
                    <div class="tab-pane fade" id="license" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="lisence_dimiliki" class="form-label">Lisensi Dimiliki</label>
                                <textarea class="form-control" id="lisence_dimiliki" name="lisence_dimiliki" rows="2">{{ old('lisence_dimiliki') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <input type="text" class="form-control" id="rating" name="rating"
                                    value="{{ old('rating') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_stkp" class="form-label">No STKP</label>
                                <input type="text" class="form-control" id="no_stkp" name="no_stkp"
                                    value="{{ old('no_stkp') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="masa_berlaku" class="form-label">Masa Berlaku</label>
                                <input type="date" class="form-control" id="masa_berlaku" name="masa_berlaku"
                                    value="{{ old('masa_berlaku') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lisence_dibayarkan_januari" class="form-label">Lisensi Dibayarkan
                                    Januari</label>
                                <input type="text" class="form-control" id="lisence_dibayarkan_januari"
                                    name="lisence_dibayarkan_januari" value="{{ old('lisence_dibayarkan_januari') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="cek_lisence_serkom" class="form-label">Cek Lisensi/Serkom</label>
                                <textarea class="form-control" id="cek_lisence_serkom" name="cek_lisence_serkom" rows="2">{{ old('cek_lisence_serkom') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 7: Kontak -->
                    <div class="tab-pane fade" id="contact" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp"
                                    value="{{ old('no_hp') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    @push('body-scripts')
                        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
                        <script>
                            function updateFormasiFields() {
                                var select = document.getElementById('formasi_select');
                                var selected = select.options[select.selectedIndex];
                                document.getElementById('kode_jabatan').value = selected.getAttribute('data-kode_jabatan') || '';
                                document.getElementById('lokasi_kerja').value = selected.getAttribute('data-lokasi_kerja') || '';
                                document.getElementById('unit_kerja').value = selected.getAttribute('data-unit_kerja') || '';
                                document.getElementById('jabatan').value = selected.getAttribute('data-jabatan') || '';
                                document.getElementById('kelompok_kelas_jabatan').value = selected.getAttribute('data-kkj') || '';

                                // Update field Unit Hierarchy (jika ada di form)
                                var unitDeputyEgm = document.getElementById('unit_deputy_egm');
                                var unitAssistantDeputy = document.getElementById('unit_assistant_deputy');
                                var unitDivisionHead = document.getElementById('unit_division_head');
                                var unitDepartmentHead = document.getElementById('unit_department_head');

                                if (unitDeputyEgm) unitDeputyEgm.value = selected.getAttribute('data-unit_deputy_egm') || '';
                                if (unitAssistantDeputy) unitAssistantDeputy.value = selected.getAttribute('data-unit_assistant_deputy') || '';
                                if (unitDivisionHead) unitDivisionHead.value = selected.getAttribute('data-unit_division_head') || '';
                                if (unitDepartmentHead) unitDepartmentHead.value = selected.getAttribute('data-unit_department_head') || '';
                            }
                            document.addEventListener('DOMContentLoaded', function() {
                                const formasiSelect = document.getElementById('formasi_select');
                                if (formasiSelect) {
                                    new Choices(formasiSelect, {
                                        searchEnabled: true,
                                        itemSelectText: '',
                                        shouldSort: false
                                    });
                                }
                            });
                        </script>
                    @endpush
                    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
