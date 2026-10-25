@extends('layouts.app')

@section('title', 'Detail Audit Log')

@section('header-title', 'Detail Audit Log')

@push('head-scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .detail-card {
            border-radius: 8px;
        }

        .json-viewer {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 1rem;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        .change-table td {
            vertical-align: top;
            padding: 0.75rem;
        }

        .old-value {
            color: #dc3545;
            background: #f8d7da;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .new-value {
            color: #28a745;
            background: #d4edda;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .bg-purple {
            background-color: #6f42c1 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Back Button --}}
        <div class="mb-3">
            <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Audit Log
            </a>
        </div>

        {{-- Main Info Card --}}
        <div class="card detail-card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informasi Utama
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Aksi:</th>
                                <td>
                                    <span class="badge bg-{{ $auditLog->action_badge_color }} fs-6">
                                        {{ $auditLog->action_name }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tipe Data:</th>
                                <td><strong>{{ $auditLog->model_name }}</strong></td>
                            </tr>
                            <tr>
                                <th>ID Record:</th>
                                <td><code>{{ $auditLog->model_id ?? '-' }}</code></td>
                            </tr>
                            <tr>
                                <th>Identifier:</th>
                                <td><strong>{{ $auditLog->model_identifier ?? '-' }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">User:</th>
                                <td><strong>{{ $auditLog->user_name ?? 'System' }}</strong></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $auditLog->user_email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Waktu:</th>
                                <td><strong>{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</strong></td>
                            </tr>
                            <tr>
                                <th>IP Address:</th>
                                <td><code>{{ $auditLog->ip_address ?? '-' }}</code></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Changes Card (for updates) --}}
        @if ($auditLog->action === 'updated' && !empty($auditLog->changes))
            <div class="card detail-card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Perubahan Detail
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped change-table">
                        <thead>
                            <tr>
                                <th style="width: 30%">Field</th>
                                <th style="width: 35%">Nilai Lama</th>
                                <th style="width: 35%">Nilai Baru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($auditLog->getChangesSummary() as $change)
                                <tr>
                                    <td><strong>{{ $change['field'] }}</strong></td>
                                    @if (isset($change['is_sensitive']) && $change['is_sensitive'])
                                        <td colspan="2" class="text-center">
                                            <span class="badge bg-warning text-dark fs-6">
                                                <i class="bi bi-shield-lock"></i> Data Sensitif - Tidak Ditampilkan
                                            </span>
                                        </td>
                                    @else
                                        <td><span class="old-value">{{ $change['old'] }}</span></td>
                                        <td><span class="new-value">{{ $change['new'] }}</span></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Old Values (for deleted) --}}
        @if ($auditLog->action === 'deleted' && !empty($auditLog->old_values))
            <div class="card detail-card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-trash"></i> Data Yang Dihapus
                    </h5>
                </div>
                <div class="card-body">
                    <div class="json-viewer">
                        <pre>{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        @endif

        {{-- New Values (for created) --}}
        @if ($auditLog->action === 'created' && !empty($auditLog->new_values))
            <div class="card detail-card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Data Yang Ditambahkan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="json-viewer">
                        <pre>{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        @endif

        {{-- Bulk Import Details --}}
        @if (in_array($auditLog->action, ['bulk_created', 'bulk_replaced']) && !empty($auditLog->new_values))
            <div class="card detail-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-arrow-up"></i> Detail Bulk Import
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Mode Import:</th>
                            <td>
                                <span
                                    class="badge bg-{{ $auditLog->action === 'bulk_replaced' ? 'dark' : 'primary' }} fs-6">
                                    {{ $auditLog->new_values['mode'] === 'replace' ? 'REPLACE (Ganti Semua)' : 'ADD (Tambah Data)' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Nama File:</th>
                            <td><code>{{ $auditLog->new_values['filename'] ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <th>Data Berhasil Di-import:</th>
                            <td>
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-check-circle"></i> {{ $auditLog->new_values['imported'] ?? 0 }} data
                                </span>
                            </td>
                        </tr>
                        @if (isset($auditLog->new_values['skipped']) && $auditLog->new_values['skipped'] > 0)
                            <tr>
                                <th>Data Dilewati:</th>
                                <td>
                                    <span class="badge bg-warning text-dark fs-6">
                                        <i class="bi bi-exclamation-triangle"></i> {{ $auditLog->new_values['skipped'] }}
                                        baris
                                    </span>
                                    <small class="text-muted d-block mt-1">
                                        (NIK duplikat atau data tidak valid)
                                    </small>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th>Total Data Setelah Import:</th>
                            <td><strong>{{ number_format($auditLog->new_values['total_after'] ?? 0) }} records</strong>
                            </td>
                        </tr>
                        @if ($auditLog->action === 'bulk_replaced' && isset($auditLog->old_values['total_records']))
                            <tr>
                                <th>Total Data Sebelum Replace:</th>
                                <td class="text-danger">
                                    <del>{{ number_format($auditLog->old_values['total_records']) }} records</del>
                                    <small class="text-muted d-block">(Data lama dihapus)</small>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        {{-- Version Restore Details --}}
        @if ($auditLog->action === 'version_restored' && !empty($auditLog->new_values))
            <div class="card detail-card mb-4">
                <div class="card-header text-white" style="background-color: #6f42c1;">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i> Detail Restore Snapshot
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Deskripsi Snapshot:</th>
                            <td><strong>{{ $auditLog->new_values['description'] ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal Snapshot:</th>
                            <td>
                                <span class="badge bg-info fs-6">
                                    <i class="bi bi-calendar-check"></i>
                                    {{ $auditLog->new_values['snapshot_date'] ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah Data Di-restore:</th>
                            <td>
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-database-fill-check"></i>
                                    {{ number_format($auditLog->new_values['records_count'] ?? 0) }} records
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2" class="pt-3">
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Perhatian:</strong> Operasi restore menghapus semua data karyawan yang ada dan
                                    menggantinya dengan data dari snapshot.
                                </div>
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        {{-- User Agent --}}
        @if ($auditLog->user_agent)
            <div class="card detail-card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-square"></i> Informasi Browser/Device
                    </h5>
                </div>
                <div class="card-body">
                    <code>{{ $auditLog->user_agent }}</code>
                </div>
            </div>
        @endif

    </div>
@endsection
