<div class="row g-0">
    <div class="col-12">
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">NIK</label>
                    <div class="form-control-plaintext bg-light rounded px-3 py-2">
                        <span class="badge bg-primary">{{ $dataPgs->nik }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">Nama</label>
                    <div class="form-control-plaintext bg-light rounded px-3 py-2">
                        {{ $dataPgs->nama }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">Jabatan Definitif</label>
                    <div class="form-control-plaintext bg-light rounded px-3 py-2">
                        {{ $dataPgs->jabatan_definitif }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">Jabatan PGS</label>
                    <div class="form-control-plaintext bg-light rounded px-3 py-2">
                        {{ $dataPgs->jabatan_pgs }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">Lokasi/Unit Kerja</label>
                    <div class="form-control-plaintext bg-light rounded px-3 py-2">
                        {{ $dataPgs->lokasi_unit_kerja }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">Tanggal PGS</label>
                    <div class="form-control-plaintext bg-light rounded px-3 py-2">
                        <i class="bi bi-calendar3 text-muted me-2"></i>{{ $dataPgs->tanggal_pgs_formatted }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-slate-700 mb-1">Durasi PGS</label>
                    <div class="form-control-plaintext bg-light rounded px-3 py-2">
                        <span class="badge bg-success">{{ $dataPgs->durasi_pgs }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer border-0 bg-light">
    <div class="d-flex gap-2 w-100 justify-content-end">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i>Tutup
        </button>

        @can('admin')
            <a href="{{ route('data-pgs.edit', $dataPgs) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit Data
            </a>
        @endcan
    </div>
</div>
