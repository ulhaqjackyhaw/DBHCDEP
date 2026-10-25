@extends('layouts.app')

@section('title', 'Manajemen Snapshot')
@section('header-title', 'Manajemen Snapshot Data Karyawan')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form untuk membuat snapshot baru --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="mb-0">Buat Snapshot Baru</h5>
        </div>
        <div class="card-body">
            <p class="card-text text-muted">
                Fitur ini akan mengambil "fotokopi" dari seluruh data karyawan saat ini dan menyimpannya sebagai file Excel yang bisa Anda download kapan saja.
            </p>
            <form action="{{ route('snapshots.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi (Opsional)</label>
                    <input type="text" class="form-control" id="description" name="description" placeholder="Contoh: Snapshot sebelum update data diganti massal">
                </div>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-camera-fill"></i>
                    <span>Buat Snapshot Sekarang</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Tabel daftar snapshot --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="mb-0">Daftar Snapshot Tersimpan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Waktu Dibuat</th>
                            <th>Deskripsi</th>
                            <th>Nama File</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($snapshots as $snapshot)
                            <tr>
                                <td class="text-nowrap">{{ $snapshot->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $snapshot->description }}</td>
                                <td class="text-muted">{{ $snapshot->file_name }}</td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('snapshots.download', $snapshot) }}" class="btn btn-sm btn-success" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="{{ route('snapshots.destroy', $snapshot) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus snapshot ini secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    Belum ada snapshot yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $snapshots->links() }}
            </div>
        </div>
    </div>
@endsection