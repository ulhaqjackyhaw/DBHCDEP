@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('header-title', 'Profil Pengguna')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.min.css">
    <style>
        .crop-container {
            max-width: 100%;
            max-height: 400px;
            overflow: hidden;
        }

        #cropImage {
            max-width: 100%;
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                {{-- Informasi Profil --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Informasi Profil</h5>
                    </div>
                    <div class="card-body">
                        @if (session('profile_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('profile_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Foto Profil Section --}}
                        <div class="row align-items-center mb-4 pb-4 border-bottom">
                            <div class="col-md-3 text-center">
                                <img src="{{ auth()->user()->getPhotoUrl() }}" alt="Foto profil" class="rounded-circle mb-3"
                                    width="120" height="120" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-1">Role</h6>
                                    <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'primary' : 'success' }}">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h6 class="mb-3">Foto Profil</h6>

                                {{-- Upload Foto --}}
                                <form action="{{ route('profile.upload-photo') }}" method="POST"
                                    enctype="multipart/form-data" class="mb-3" id="uploadForm">
                                    @csrf
                                    <div class="input-group">
                                        <input type="file" class="form-control @error('photo') is-invalid @enderror"
                                            id="photoInput" name="photo_temp" accept="image/jpeg,image/jpg,image/png">
                                        <input type="hidden" name="photo" id="croppedImage">
                                        <button type="button" class="btn btn-primary" id="uploadBtn" disabled>
                                            <i class="bi bi-upload me-1"></i>Upload
                                        </button>
                                    </div>
                                    @error('photo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal: 2MB. Foto akan di-crop
                                        1:1</small>
                                </form>

                                {{-- Pilih Avatar --}}
                                <h6 class="mb-2 mt-4">Atau Pilih Avatar</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <form action="{{ route('profile.select-avatar') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="avatar"
                                                value="avatars/avatar{{ $i }}.png">
                                            <button type="submit" class="btn btn-outline-secondary p-1 border-0"
                                                title="Pilih avatar ini">
                                                <img src="{{ asset('images/avatars/avatar' . $i . '.png') }}"
                                                    alt="Avatar {{ $i }}" width="50" height="50"
                                                    class="rounded-circle" style="cursor: pointer;">
                                            </button>
                                        </form>
                                    @endfor
                                </div>

                                {{-- Hapus Foto --}}
                                @if (auth()->user()->photo)
                                    <form action="{{ route('profile.delete-photo') }}" method="POST" class="mt-3"
                                        id="deletePhotoForm">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="deletePhotoBtn">
                                            <i class="bi bi-trash me-1"></i>Hapus Foto
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', auth()->user()->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', auth()->user()->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-check me-2"></i>Update Profil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Form Ganti Password --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Ganti Password</h5>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('profile.update-password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <input type="password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-key me-2"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Crop Foto --}}
    <div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="crop-container">
                        <img id="cropImage" src="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="cropButton">
                        <i class="bi bi-check-circle me-1"></i>Crop & Upload
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.all.min.js"></script>
    <script>
        let cropper;
        const photoInput = document.getElementById('photoInput');
        const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
        const cropImage = document.getElementById('cropImage');
        const cropButton = document.getElementById('cropButton');
        const uploadBtn = document.getElementById('uploadBtn');
        const uploadForm = document.getElementById('uploadForm');
        const croppedImageInput = document.getElementById('croppedImage');

        // Handle file selection
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (!file) return;

            // Validate file type
            if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Valid',
                    text: 'Format file harus JPG, JPEG, atau PNG',
                    confirmButtonColor: '#0d6efd'
                });
                photoInput.value = '';
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                Swal.fire({
                    icon: 'warning',
                    title: 'File Terlalu Besar',
                    html: `Ukuran file: <strong>${fileSizeMB} MB</strong><br>Maksimal: <strong>2 MB</strong><br><br>Silakan pilih foto yang lebih kecil`,
                    confirmButtonColor: '#0d6efd'
                });
                photoInput.value = '';
                return;
            }

            // Read file and show in modal
            const reader = new FileReader();
            reader.onload = function(event) {
                cropImage.src = event.target.result;
                cropModal.show();

                // Initialize cropper after modal is shown
                document.getElementById('cropModal').addEventListener('shown.bs.modal', function() {
                    if (cropper) {
                        cropper.destroy();
                    }

                    cropper = new Cropper(cropImage, {
                        aspectRatio: 1,
                        viewMode: 2,
                        dragMode: 'move',
                        autoCropArea: 1,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                }, {
                    once: true
                });
            };
            reader.readAsDataURL(file);
        });

        // Handle crop button
        cropButton.addEventListener('click', function() {
            if (!cropper) return;

            // Get cropped canvas
            const canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            // Convert to blob and set to hidden input
            canvas.toBlob(function(blob) {
                const reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function() {
                    croppedImageInput.value = reader.result;
                    uploadBtn.disabled = false;
                    cropModal.hide();

                    // Show success message
                    const fileName = photoInput.files[0].name;
                    const inputGroup = photoInput.parentElement;
                    let successMsg = inputGroup.querySelector('.crop-success');
                    if (!successMsg) {
                        successMsg = document.createElement('small');
                        successMsg.className = 'crop-success d-block text-success mt-1';
                        inputGroup.appendChild(successMsg);
                    }
                    successMsg.textContent = `✓ ${fileName} siap di-upload (sudah di-crop 1:1)`;
                };
            }, 'image/jpeg', 0.9);
        });

        // Handle upload button
        uploadBtn.addEventListener('click', function() {
            if (croppedImageInput.value) {
                uploadForm.submit();
            }
        });

        // Reset on modal close
        document.getElementById('cropModal').addEventListener('hidden.bs.modal', function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            if (!croppedImageInput.value) {
                photoInput.value = '';
            }
        });

        // Handle delete photo button
        const deletePhotoBtn = document.getElementById('deletePhotoBtn');
        if (deletePhotoBtn) {
            deletePhotoBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Hapus Foto Profil?',
                    text: 'Foto profil Anda akan dihapus dan kembali ke avatar default',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deletePhotoForm').submit();
                    }
                });
            });
        }
    </script>
@endpush
