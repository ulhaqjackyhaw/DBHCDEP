@extends('layouts.app')

@section('title', 'Import Data Karyawan')
@section('header-title', 'Import Data Karyawan')

@push('head-styles')
    <style>
        .progress-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 2rem;
        }

        .progress {
            height: 30px;
            border-radius: 15px;
            overflow: visible;
            background-color: #e9ecef;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: width 0.3s ease;
            background: linear-gradient(90deg, #0d6efd 0%, #0a58ca 100%);
            box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3);
        }

        .status-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .spinner-border {
            width: 60px;
            height: 60px;
            border-width: 6px;
        }

        .message-box {
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .success-box {
            border-left-color: #198754;
            background: #d1e7dd;
        }

        .error-box {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
    </style>
@endpush

@section('content')
    <div class="progress-container">
        <div class="card shadow-lg border-0">
            <div class="card-body text-center p-5">
                <div id="statusIcon" class="status-icon">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <h3 class="mb-4" id="statusTitle">Memproses Import Data...</h3>

                <div class="progress mb-3">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                        style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>

                <div id="messageBox" class="message-box">
                    <p class="mb-0" id="statusMessage">Menginisialisasi proses import...</p>
                </div>

                <div id="skipDetails" class="mt-3" style="display: none;">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Detail Import:</h6>
                        <div id="skipSummary"></div>
                        <hr>
                        <button class="btn btn-sm btn-outline-warning" type="button" data-bs-toggle="collapse"
                            data-bs-target="#skipDetailList" aria-expanded="false">
                            <i class="bi bi-list"></i> Lihat Baris yang Dilewati
                        </button>
                        <div class="collapse mt-3" id="skipDetailList">
                            <div id="emptyNikList" class="mb-3"></div>
                        </div>
                    </div>
                </div>

                <div id="actionButtons" class="mt-4" style="display: none;">
                    <a href="{{ route('karyawan.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Data Karyawan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Versioning -->
    <div class="modal fade" id="versioningModal" tabindex="-1" aria-labelledby="versioningModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="versioningModalLabel">
                        <i class="bi bi-archive-fill me-2"></i>Simpan Versi Data Karyawan?
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('versions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Import data berhasil! Simpan kondisi data saat ini sebagai "save point" yang bisa dipulihkan
                            nanti?</p>
                        <div class="mb-3">
                            <label for="versionDescription" class="form-label">Deskripsi / Catatan Perubahan
                                (Opsional)</label>
                            <input type="text" class="form-control" id="versionDescription" name="description"
                                placeholder="Contoh: Import data bulanan Januari 2024">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            onclick="window.location.href='{{ route('karyawan.index') }}'">
                            <i class="bi bi-x-circle me-1"></i>Lewati
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Ya, Simpan Versi Ini
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('body-scripts')
    <script>
        const sessionId = "{{ $sessionId }}";
        let checkInterval;

        function updateProgress() {
            fetch(`/data-karyawan/import-progress/${sessionId}`)
                .then(response => response.json())
                .then(data => {
                    const progress = data.progress || 0;
                    const status = data.status || 'processing';
                    const message = data.message || 'Memproses...';

                    // Update progress bar
                    const progressBar = document.getElementById('progressBar');
                    progressBar.style.width = progress + '%';
                    progressBar.setAttribute('aria-valuenow', progress);
                    progressBar.textContent = Math.round(progress) + '%';

                    // Update message
                    document.getElementById('statusMessage').textContent = message;

                    // Handle not found
                    if (status === 'not_found') {
                        clearInterval(checkInterval);

                        progressBar.classList.remove('progress-bar-animated');
                        progressBar.classList.add('bg-warning');

                        document.getElementById('statusIcon').innerHTML =
                            '<i class="bi bi-exclamation-triangle-fill text-warning"></i>';
                        document.getElementById('statusTitle').textContent = 'Session Tidak Ditemukan';

                        const messageBox = document.getElementById('messageBox');
                        messageBox.classList.add('error-box');
                        messageBox.style.borderLeftColor = '#ffc107';
                        messageBox.style.background = '#fff3cd';

                        document.getElementById('actionButtons').style.display = 'block';
                    }
                    // Handle completion
                    else if (status === 'completed') {
                        clearInterval(checkInterval);

                        progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                        progressBar.classList.add('bg-success');

                        document.getElementById('statusIcon').innerHTML =
                            '<i class="bi bi-check-circle-fill text-success"></i>';
                        document.getElementById('statusTitle').textContent = 'Import Berhasil!';

                        const messageBox = document.getElementById('messageBox');
                        messageBox.classList.add('success-box');

                        // Show skip details if any
                        if (data.skipped && data.skipped > 0 && data.skipped_details) {
                            const skipDetails = document.getElementById('skipDetails');
                            const skipSummary = document.getElementById('skipSummary');
                            const emptyNikList = document.getElementById('emptyNikList');

                            const emptyCount = data.skipped_details.empty_nik?.length || 0;

                            skipSummary.innerHTML = `
                                <p class="mb-1"><strong>📊 Total berhasil:</strong> <span class="badge bg-success">${data.imported || 0} data</span></p>
                                <p class="mb-1"><strong>⚠️ Total dilewati:</strong> <span class="badge bg-warning">${data.skipped} baris</span></p>
                                <p class="mb-0"><strong>📝 Total diproses:</strong> <span class="badge bg-info">${data.total_processed || 0} baris</span></p>
                                <hr>
                                <p class="mb-0 text-muted small">
                                    <i class="bi bi-info-circle"></i> NIK duplikat otomatis di-UPDATE dengan data terbaru dari Excel.
                                </p>
                            `;

                            // Empty NIK list
                            if (emptyCount > 0) {
                                let emptyHtml =
                                    `<h6><i class="bi bi-x-circle text-danger"></i> Baris dengan NIK Kosong (${emptyCount} baris):</h6><ul class="mb-0" style="max-height: 200px; overflow-y: auto;">`;
                                data.skipped_details.empty_nik.forEach(item => {
                                    emptyHtml += `<li>Baris ${item.row}: ${item.nama} - ${item.reason}</li>`;
                                });
                                emptyHtml += '</ul>';
                                emptyNikList.innerHTML = emptyHtml;
                            }

                            skipDetails.style.display = 'block';
                        }

                        document.getElementById('actionButtons').style.display = 'block';

                        // Show versioning modal after import completes
                        setTimeout(() => {
                            const versioningModalElement = document.getElementById('versioningModal');
                            if (versioningModalElement) {
                                const versioningModal = new bootstrap.Modal(versioningModalElement);
                                versioningModal.show();

                                // Focus on description input when modal is shown
                                versioningModalElement.addEventListener('shown.bs.modal', () => {
                                    document.getElementById('versionDescription').focus();
                                });
                            }
                        }, 1500);
                    }
                    // Handle error
                    else if (status === 'error') {
                        clearInterval(checkInterval);

                        progressBar.classList.remove('progress-bar-animated');
                        progressBar.classList.add('bg-danger');

                        document.getElementById('statusIcon').innerHTML =
                            '<i class="bi bi-x-circle-fill text-danger"></i>';
                        document.getElementById('statusTitle').textContent = 'Import Gagal';

                        const messageBox = document.getElementById('messageBox');
                        messageBox.classList.add('error-box');

                        document.getElementById('actionButtons').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error checking progress:', error);
                    clearInterval(checkInterval);

                    document.getElementById('statusIcon').innerHTML =
                        '<i class="bi bi-exclamation-circle-fill text-danger"></i>';
                    document.getElementById('statusTitle').textContent = 'Koneksi Error';
                    document.getElementById('statusMessage').textContent = 'Tidak dapat terhubung ke server: ' + error
                        .message;

                    const messageBox = document.getElementById('messageBox');
                    messageBox.classList.add('error-box');
                    document.getElementById('actionButtons').style.display = 'block';
                });
        }

        // Start checking progress every 500ms
        checkInterval = setInterval(updateProgress, 500);
        updateProgress(); // Initial check
    </script>
@endpush
